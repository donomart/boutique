<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProductsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 10; $i++) {
            $product = new Product();
            $product->setName($faker->words(3, true))
                ->setDescription($faker->paragraph(2))
                ->setPrice($faker->numberBetween(10, 20000))
                ->setSlug($faker->slug(2))
                ->setSubtitle($faker->words(5, true))
                ->setIllustration(
                    $faker->image(
                        'public/uploads',
                        360,
                        360,
                        'product',
                        false,
                        true,
                        'cats',
                        true
                    )
                )->setCategory($this->getReference('category_' . $faker->numberBetween(1, 10)));


            $manager->persist($product);
        }


        $manager->flush();
    }
}
