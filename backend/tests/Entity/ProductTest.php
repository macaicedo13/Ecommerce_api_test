<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

/**
 * ProductTest
 * Unit tests for Product entity
 */
class ProductTest extends TestCase
{
    public function testProductCreation(): void
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setDescription('Test Description');
        $product->setPrice('99.99');
        $product->setStock(10);

        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals('Test Description', $product->getDescription());
        $this->assertEquals('99.99', $product->getPrice());
        $this->assertEquals(10, $product->getStock());
        $this->assertInstanceOf(\DateTimeInterface::class, $product->getCreatedAt());
        $this->assertInstanceOf(\DateTimeInterface::class, $product->getUpdatedAt());
    }

    public function testDecreaseStock(): void
    {
        $product = new Product();
        $product->setStock(10);

        $product->decreaseStock(3);
        $this->assertEquals(7, $product->getStock());

        $product->decreaseStock(7);
        $this->assertEquals(0, $product->getStock());
    }

    public function testDecreaseStockThrowsExceptionOnInsufficientStock(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Stock insuficiente/');

        $product = new Product();
        $product->setName('Test Product');
        $product->setStock(5);

        $product->decreaseStock(10);
    }

    public function testHasStock(): void
    {
        $product = new Product();
        $product->setStock(10);

        $this->assertTrue($product->hasStock(5));
        $this->assertTrue($product->hasStock(10));
        $this->assertFalse($product->hasStock(11));
    }
}
