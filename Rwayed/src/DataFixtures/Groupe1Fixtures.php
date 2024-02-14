<?php

namespace App\DataFixtures;

use App\Entity\Personne;
use App\Services\PasswordHasherService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

;

class Groupe1Fixtures extends Fixture
{
    private PasswordHasherService $passwordHasherService;

    public function __construct(PasswordHasherService $passwordHasherService)
    {
        $this->passwordHasherService = $passwordHasherService;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $roles = ['admin', 'adherent', 'technicien'];
        for ($i = 0; $i < 20; $i++) {
            $personne = new Personne();
            $personne->setNom($faker->firstName);
            $personne->setPrenom($faker->lastName);

            $numero = $faker->randomElement(['06', '07']) . $faker->numberBetween(1000000, 9999999);
            $personne->setTele($numero);

            $role = $faker->randomElement($roles);
            $personne->setRole($role);
            $personne->setEmail($faker->email);

            $dateNaissance = $faker->dateTimeBetween('-60 years', '-18 years');
            $personne->setDateNaissance($dateNaissance);

            $hashedPassword = $this->passwordHasherService->hashPassword($personne, $faker->password);
            $personne->setPassword($hashedPassword);

            $personne->setDerniereConnection($faker->dateTimeThisYear);
            $personne->setLocked(false);

            $manager->persist($personne);
            $this->addReference('personne_' . $i, $personne);
        }

        $manager->flush();
    }
}
