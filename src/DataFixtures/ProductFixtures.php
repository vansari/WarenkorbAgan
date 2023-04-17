<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\SerializerInterface;

class ProductFixtures extends Fixture
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = [
            [
                'name' => 'Schuhe',
                'price' => 199.99,
            ],
            [
                'name' => 'Rock',
                'price' => 99.99,
            ],
            [
                'name' => 'Telefon',
                'price' => 9.99,
            ],
            [
                'name' => 'Computer',
                'price' => 1099.99,
            ],
        ];

        foreach ($data as $productData) {
            $product = $this->serializer->deserialize(json_encode($productData), Product::class, 'json');
            $manager->persist($product);
        }

        $manager->flush();
    }
}
