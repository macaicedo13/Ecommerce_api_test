<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * AppFixtures
 * Load sample data for testing
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create sample products
        $products = [
            [
                'name' => 'Laptop Pro 15"',
                'description' => 'High-performance laptop with 16GB RAM and 512GB SSD',
                'price' => '1299.99',
                'stock' => 25
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse with precision tracking',
                'price' => '29.99',
                'stock' => 100
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'RGB backlit mechanical keyboard with blue switches',
                'price' => '89.99',
                'stock' => 50
            ],
            [
                'name' => 'USB-C Hub',
                'description' => '7-in-1 USB-C hub with HDMI, USB 3.0, and SD card reader',
                'price' => '45.99',
                'stock' => 75
            ],
            [
                'name' => '27" 4K Monitor',
                'description' => '4K UHD monitor with IPS panel and 60Hz refresh rate',
                'price' => '399.99',
                'stock' => 30
            ],
            [
                'name' => 'Webcam HD 1080p',
                'description' => 'HD webcam with autofocus and built-in microphone',
                'price' => '59.99',
                'stock' => 60
            ],
            [
                'name' => 'Desk Lamp LED',
                'description' => 'Adjustable LED desk lamp with touch control',
                'price' => '34.99',
                'stock' => 40
            ],
            [
                'name' => 'External SSD 1TB',
                'description' => 'Portable external SSD with USB 3.1 Gen 2 interface',
                'price' => '149.99',
                'stock' => 45
            ],
            [
                'name' => 'Headphones Wireless',
                'description' => 'Noise-cancelling Bluetooth headphones with 30h battery',
                'price' => '179.99',
                'stock' => 35
            ],
            [
                'name' => 'Phone Stand',
                'description' => 'Aluminum phone stand with adjustable angle',
                'price' => '19.99',
                'stock' => 200
            ],
            [
                'name' => 'Cable Organizer Set',
                'description' => 'Cable management kit with clips and sleeves',
                'price' => '12.99',
                'stock' => 150
            ],
            [
                'name' => 'Portable Charger 20000mAh',
                'description' => 'High-capacity power bank with fast charging',
                'price' => '49.99',
                'stock' => 80
            ],
        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->setName($productData['name']);
            $product->setDescription($productData['description']);
            $product->setPrice($productData['price']);
            $product->setStock($productData['stock']);
            
            $manager->persist($product);
        }

        $manager->flush();
    }
}
