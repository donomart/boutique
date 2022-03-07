<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        $admin = new User();
        $admin->setEmail('admin@mail.com')
            ->setPassword($this->passwordHasher->hashPassword(
                $admin,
                "a"
            ))
            ->setFirstName('admin')
            ->setLastName('admin')
            ->setRoles(["ROLE_ADMIN"])
            ->setActive(true);
            
        $manager->persist($admin);

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email())
                ->setPassword($this->passwordHasher->hashPassword(
                    $user,
                    "a"
                ))
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setRoles(["ROLE_USER"])
                ->setActive(false);

            $manager->persist($user);
        }


        $manager->flush();
    }
}
