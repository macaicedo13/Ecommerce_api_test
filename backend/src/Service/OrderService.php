<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

/**
 * OrderService
 * Maneja la lógica de negocio para la gestión de pedidos
 */
class OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Crear un nuevo pedido para un cliente
     *
     * @param string $customerId Identificador del cliente
     * @param array $items Array de ítems [{productId, quantity}, ...]
     * @return Order Pedido creado
     * @throws \Exception si el producto no se encuentra o hay stock insuficiente
     */
    public function createOrder(string $customerId, array $items): Order
    {
        $order = new Order();
        $order->setCustomerId($customerId);

        foreach ($items as $itemData) {
            $product = $this->findProductOrFail($itemData['productId']);
            
            // Validate stock availability
            if (!$product->hasStock($itemData['quantity'])) {
                throw new \Exception(sprintf(
                    'Stock insuficiente para el producto "%s". Disponible: %d, Solicitado: %d',
                    $product->getName(),
                    $product->getStock(),
                    $itemData['quantity']
                ));
            }

            // Create order item
            $orderItem = new OrderItem();
            $orderItem->setProduct($product);
            $orderItem->setQuantity($itemData['quantity']);
            $orderItem->setUnitPrice($product->getPrice());
            
            $order->addItem($orderItem);

            // Decrease product stock
            $product->decreaseStock($itemData['quantity']);
        }

        // Calculate order totals
        $order->calculateTotals();

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    public function findById(int $id): ?Order
    {
        return $this->entityManager->getRepository(Order::class)->find($id);
    }

    /**
     * Listar todos los pedidos (Admin)
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Order::class)->findBy([], ['createdAt' => 'DESC']);
    }

    /**
     * Listar pedidos de un cliente específico
     */
    public function findByCustomer(string $customerId): array
    {
        return $this->entityManager->getRepository(Order::class)->findBy(
            ['customerId' => $customerId],
            ['createdAt' => 'DESC']
        );
    }

    /**
     * Buscar producto por ID o lanzar excepción
     *
     * @throws \Exception si el producto no se encuentra
     */
    private function findProductOrFail(int $productId): Product
    {
        $product = $this->entityManager->getRepository(Product::class)->find($productId);
        
        if (!$product) {
            throw new \Exception(sprintf('Producto con ID %d no encontrado', $productId));
        }

        return $product;
    }

    /**
     * Validar que el cliente es dueño del pedido
     */
    public function validateOrderOwnership(Order $order, string $customerId): bool
    {
        return $order->getCustomerId() === $customerId;
    }
}
