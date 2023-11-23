<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         $product = new Product();
         $product->setName('Product 1');
         $product->setDescription('Description 1');
         $product->setImages([
             'product1_01.jpg',
             'product1_02.jpg',
             'product1_03.jpg',
         ]);
         $manager->persist($product);

         // -----------------------------

        $product = new Product();
        $product->setName('Product 2');
        $product->setDescription('Description 2');
        $product->setImages([
            'product2_01.jpg',
            'product2_02.jpg',
            'product2_03.jpg',
        ]);
        $manager->persist($product);

        // -----------------------------

        $product = new Product();
        $product->setName('Product 3');
        $product->setDescription('Description 3');
        $product->setImages([
            'product3_01.jpg',
            'product3_02.jpg',
            'product3_03.jpg',
        ]);
        $manager->persist($product);

        // -----------------------------

        $product = new Product();
        $product->setName('Product 4');
        $product->setDescription('Description 4');
        $product->setImages([
            'product4_01.jpg',
            'product4_02.jpg',
            'product4_03.jpg',
        ]);
        $manager->persist($product);

        // -----------------------------

        $product = new Product();
        $product->setName('Product 5');
        $product->setDescription('Description 5');
        $product->setImages([
            'product5_01.jpg',
            'product5_02.jpg',
            'product5_03.jpg',
        ]);
        $manager->persist($product);

        $manager->flush();
    }
}
