<?php

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

/**
 * OrderTest
 * Unit tests for Order entity
 */
class OrderTest extends TestCase
{
    public function testOrderCreation(): void
    {
        $order = new Order();
        $order->setCustomerId('customer123');
        $order->setStatus(Order::STATUS_PENDING);

        $this->assertEquals('customer123', $order->getCustomerId());
        $this->assertEquals(Order::STATUS_PENDING, $order->getStatus());
        $this->assertEquals('0.00', $order->getSubtotal());
        $this->assertEquals('0.00', $order->getTax());
        $this->assertEquals('0.00', $order->getTotal());
    }

    public function testCalculateTotals(): void
    {
        $order = new Order();
        $order->setCustomerId('customer123');

        // Create mock product
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice('100.00');
        $product->setStock(10);

        // Add order item
        $orderItem = new OrderItem();
        $orderItem->setProduct($product);
        $orderItem->setQuantity(2);
        $orderItem->setUnitPrice('100.00');
        $order->addItem($orderItem);

        $order->calculateTotals();

        // Subtotal: 2 * 100 = 200
        // Tax: 200 * 0.15 = 30
        // Total: 200 + 30 = 230
        $this->assertEquals('200.00', $order->getSubtotal());
        $this->assertEquals('30.00', $order->getTax());
        $this->assertEquals('230.00', $order->getTotal());
    }

    public function testCanBeModified(): void
    {
        $order = new Order();
        $order->setStatus(Order::STATUS_PENDING);
        $this->assertTrue($order->canBeModified());

        $order->setStatus(Order::STATUS_COMPLETED);
        $this->assertFalse($order->canBeModified());
    }

    public function testCanBeCheckedOut(): void
    {
        $order = new Order();
        $order->setStatus(Order::STATUS_PENDING);
        
        // Empty order cannot be checked out
        $this->assertFalse($order->canBeCheckedOut());

        // Add item
        $product = new Product();
        $product->setName('Test');
        $product->setPrice('10.00');
        $product->setStock(5);

        $orderItem = new OrderItem();
        $orderItem->setProduct($product);
        $orderItem->setQuantity(1);
        $orderItem->setUnitPrice('10.00');
        $order->addItem($orderItem);

        $this->assertTrue($order->canBeCheckedOut());

        // Completed order cannot be checked out again
        $order->setStatus(Order::STATUS_COMPLETED);
        $this->assertFalse($order->canBeCheckedOut());
    }
}
