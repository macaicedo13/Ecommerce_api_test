<?php

namespace App\Controller;

use App\Service\OrderService;
use App\Service\CheckoutService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use App\Entity\Order;

/**
 * OrderController
 * Maneja los endpoints de gestión de pedidos
 */
#[Route('/api/orders')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderService $orderService,
        private CheckoutService $checkoutService
    ) {
    }

    /**
     * Listar pedidos (Admin ve todos, Cliente solo los suyos)
     */
    #[Route('', name: 'api_orders_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Devuelve la lista de pedidos',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'orders', type: 'array', items: new OA\Items(ref: new Model(type: Order::class)))
            ]
        )
    )]
    #[OA\Security(name: 'AuthHeader')]
    #[OA\Tag(name: 'Pedidos')]
    public function list(Request $request): JsonResponse
    {
        $role = $request->headers->get('X-Role');
        $customerId = $request->headers->get('X-Customer-Id');

        if (empty($role) || empty($customerId)) {
            return $this->json([
                'error' => 'No autorizado',
                'message' => 'Headers X-Role y X-Customer-Id son obligatorios'
            ], 401);
        }

        if ($role === 'admin') {
            $orders = $this->orderService->findAll();
        } else {
            $orders = $this->orderService->findByCustomer($customerId);
        }

        return $this->json([
            'orders' => array_map(fn($o) => $this->serializeOrder($o), $orders)
        ]);
    }

    /**
     * Crear un nuevo pedido
     */
    #[Route('', name: 'api_orders_create', methods: ['POST'])]
    #[OA\Response(
        response: 201,
        description: 'Pedido creado exitosamente',
        content: new OA\JsonContent(ref: new Model(type: Order::class))
    )]
    #[OA\Response(response: 400, description: 'Entrada inválida o error de stock')]
    #[OA\Response(response: 401, description: 'No autorizado')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'items', type: 'array', items: new OA\Items(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'productId', type: 'integer'),
                        new OA\Property(property: 'quantity', type: 'integer')
                    ]
                ))
            ]
        )
    )]
    #[OA\Security(name: 'CustomerHeader')]
    #[OA\Tag(name: 'Pedidos')]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        // Obtener ID del cliente de los headers
        $customerId = $request->headers->get('X-Customer-Id');
        if (empty($customerId)) {
            return $this->json([
                'error' => 'No autorizado',
                'message' => 'El header X-Customer-Id es obligatorio'
            ], 401);
        }

        $data = json_decode($request->getContent(), true);

        // Validar entrada
        $constraints = new Assert\Collection([
            'items' => [
                new Assert\NotBlank(message: 'Los ítems son obligatorios'),
                new Assert\Type('array'),
                new Assert\Count(['min' => 1, 'minMessage' => 'Se requiere al menos un ítem']),
                new Assert\All([
                    new Assert\Collection([
                        'productId' => [
                            new Assert\NotBlank(),
                            new Assert\Type('integer')
                        ],
                        'quantity' => [
                            new Assert\NotBlank(),
                            new Assert\Positive(message: 'La cantidad debe ser mayor a 0')
                        ]
                    ])
                ])
            ]
        ]);

        $violations = $validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            
            return $this->json([
                'error' => 'Error de validación',
                'details' => $errors
            ], 400);
        }

        try {
            $order = $this->orderService->createOrder($customerId, $data['items']);
            
            return $this->json([
                'message' => 'Pedido creado exitosamente',
                'order' => $this->serializeOrder($order)
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al crear el pedido',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obtener detalles del pedido
     */
    #[Route('/{id}', name: 'api_orders_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Devuelve los detalles del pedido',
        content: new OA\JsonContent(ref: new Model(type: Order::class))
    )]
    #[OA\Response(response: 401, description: 'No autorizado')]
    #[OA\Response(response: 403, description: 'Prohibido')]
    #[OA\Response(response: 404, description: 'No encontrado')]
    #[OA\Security(name: 'CustomerHeader')]
    #[OA\Tag(name: 'Pedidos')]
    public function show(int $id, Request $request): JsonResponse
    {
        // Obtener ID del cliente de los headers
        $customerId = $request->headers->get('X-Customer-Id');
        if (empty($customerId)) {
            return $this->json([
                'error' => 'No autorizado',
                'message' => 'El header X-Customer-Id es obligatorio'
            ], 401);
        }

        $order = $this->orderService->findById($id);

        if (!$order) {
            return $this->json([
                'error' => 'No encontrado',
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        // Verificar propiedad
        if (!$this->orderService->validateOrderOwnership($order, $customerId)) {
            return $this->json([
                'error' => 'Prohibido',
                'message' => 'Solo puedes ver tus propios pedidos'
            ], 403);
        }

        return $this->json([
            'order' => $this->serializeOrder($order, true)
        ]);
    }

    /**
     * Finalizar pedido (pago simulado)
     */
    #[Route('/{id}/checkout', name: 'api_orders_checkout', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Checkout completado exitosamente',
        content: new OA\JsonContent(ref: new Model(type: Order::class))
    )]
    #[OA\Response(response: 400, description: 'Error en el checkout')]
    #[OA\Response(response: 401, description: 'No autorizado')]
    #[OA\Security(name: 'CustomerHeader')]
    #[OA\Tag(name: 'Pedidos')]
    public function checkout(int $id, Request $request): JsonResponse
    {
        // Get customer ID from headers
        $customerId = $request->headers->get('X-Customer-Id');
        if (empty($customerId)) {
            return $this->json([
                'error' => 'Unauthorized',
                'message' => 'X-Customer-Id header is required'
            ], 401);
        }

        $order = $this->orderService->findById($id);

        if (!$order) {
            return $this->json([
                'error' => 'No encontrado',
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        // Verificar propiedad
        if (!$this->orderService->validateOrderOwnership($order, $customerId)) {
            return $this->json([
                'error' => 'Prohibido',
                'message' => 'Solo puedes procesar tus propios pedidos'
            ], 403);
        }

        try {
            $updatedOrder = $this->checkoutService->processCheckout($order);
            
            return $this->json([
                'message' => 'Checkout completado exitosamente',
                'order' => $this->serializeOrder($updatedOrder, true)
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error en el checkout',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Serializar entidad pedido a array
     */
    private function serializeOrder($order, bool $includeDetails = false): array
    {
        $data = [
            'id' => $order->getId(),
            'customerId' => $order->getCustomerId(),
            'status' => $order->getStatus(),
            'subtotal' => (float) $order->getSubtotal(),
            'tax' => (float) $order->getTax(),
            'total' => (float) $order->getTotal(),
            'createdAt' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $order->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        if ($includeDetails) {
            $data['items'] = array_map(function($item) {
                return [
                    'id' => $item->getId(),
                    'productId' => $item->getProduct()->getId(),
                    'productName' => $item->getProduct()->getName(),
                    'quantity' => $item->getQuantity(),
                    'unitPrice' => (float) $item->getUnitPrice(),
                    'subtotal' => (float) $item->getSubtotal(),
                ];
            }, $order->getItems()->toArray());

            if ($order->getPayment()) {
                $data['payment'] = [
                    'id' => $order->getPayment()->getId(),
                    'status' => $order->getPayment()->getStatus(),
                    'method' => $order->getPayment()->getMethod(),
                    'amount' => (float) $order->getPayment()->getAmount(),
                    'processedAt' => $order->getPayment()->getProcessedAt()?->format('Y-m-d H:i:s'),
                ];
            }
        }

        return $data;
    }
}
