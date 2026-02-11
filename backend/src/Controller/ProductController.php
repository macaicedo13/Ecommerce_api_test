<?php

namespace App\Controller;

use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use App\Entity\Product;

/**
 * ProductController
 * Maneja los endpoints del catálogo de productos
 */
#[Route('/api/products')]
class ProductController extends AbstractController
{
    public function __construct(
        private ProductService $productService
    ) {
    }

    /**
     * Listar productos con paginación y filtrado
     */
    #[Route('', name: 'api_products_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Devuelve la lista de productos',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'products', type: 'array', items: new OA\Items(ref: new Model(type: Product::class))),
                new OA\Property(property: 'meta', type: 'object')
            ]
        )
    )]
    #[OA\Parameter(
        name: 'search',
        in: 'query',
        description: 'Palabra clave de búsqueda',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Productos')]
    public function list(Request $request): JsonResponse
    {
        $search = $request->query->get('search', '');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = min(100, max(1, (int) $request->query->get('limit', 10)));
        $sort = $request->query->get('sort', 'id:asc');

        $filters = [];
        if (!empty($search)) {
            $filters['search'] = $search;
        }

        $result = $this->productService->findAll($filters, $page, $limit, $sort);

        return $this->json([
            'products' => array_map(fn($p) => $this->serializeProduct($p), $result['data']),
            'meta' => $result['meta']
        ]);
    }

    /**
     * Crear un nuevo producto (Solo Admin)
     */
    #[Route('', name: 'api_products_create', methods: ['POST'])]
    #[OA\Response(
        response: 201,
        description: 'Producto creado exitosamente',
        content: new OA\JsonContent(ref: new Model(type: Product::class))
    )]
    #[OA\Response(response: 400, description: 'Entrada inválida')]
    #[OA\Response(response: 403, description: 'Prohibido')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'price', type: 'number', format: 'float'),
                new OA\Property(property: 'stock', type: 'integer')
            ]
        )
    )]
    #[OA\Security(name: 'AdminHeader')]
    #[OA\Tag(name: 'Productos')]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        // Verificar rol de administrador
        $role = $request->headers->get('X-Role');
        if ($role !== 'admin') {
            return $this->json([
                'error' => 'Prohibido',
                'message' => 'Solo los administradores pueden crear productos'
            ], 403);
        }

        $data = json_decode($request->getContent(), true);

        // Validar entrada
        $constraints = new Assert\Collection([
            'name' => [
                new Assert\NotBlank(message: 'El nombre del producto es obligatorio'),
                new Assert\Length(['max' => 255])
            ],
            'description' => new Assert\Optional(),
            'price' => [
                new Assert\NotBlank(message: 'El precio es obligatorio'),
                new Assert\Positive(message: 'El precio debe ser mayor a 0')
            ],
            'stock' => [
                new Assert\NotBlank(message: 'El stock es obligatorio'),
                new Assert\Type('integer'),
                new Assert\GreaterThanOrEqual(0, message: 'El stock no puede ser negativo')
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
            $product = $this->productService->create($data);
            
            return $this->json([
                'message' => 'Producto creado exitosamente',
                'product' => $this->serializeProduct($product)
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al crear el producto',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Serializar entidad producto a array
     */
    private function serializeProduct($product): array
    {
        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => (float) $product->getPrice(),
            'stock' => $product->getStock(),
            'createdAt' => $product->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $product->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
