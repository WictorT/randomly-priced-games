<?php
namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    const PRODUCTS = [
        [
            'name' => 'Fallout',
            'price' => 1.99,
        ],
        [
            'name' => 'Don’t Starve',
            'price' => 2.99,
        ],
        [
            'name' => 'Baldur’s Gate',
            'price' => 3.99,
        ],
        [
            'name' => 'Icewind Dale',
            'price' => 4.99,
        ],
        [
            'name' => 'Bloodborne',
            'price' => 5.99,
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::PRODUCTS as $product) {
            $user = (new Product())
                ->setName($product['name'])
                ->setPrice($product['price']);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
