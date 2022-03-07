<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 1; $i <= 10; $i++) {
            $category = new Category();
            $category->setName('Cat-' . $i);
            $this->addReference('category_' . $i, $category);

            $manager->persist($category);
        }

        $manager->flush();
    }
}
