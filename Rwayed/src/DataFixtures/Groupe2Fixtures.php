<?php

namespace App\DataFixtures;

use App\Entity\Adherent;
use App\Entity\Admin;
use App\Entity\Technicien;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

;

class Groupe2Fixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $rang = ["Directeur", "Manager", "Chef de service"];
        for ($i = 0; $i < 20; $i++) {
            if ($this->hasReference('personne_' . $i)) {
                $personne = $this->getReference('personne_' . $i);
                if ($personne->getRole() === 'admin') {
                    $admin = new Admin();
                    $dateEmbauche = $faker->dateTimeBetween('-10 years', 'now');
                    $admin->setDateEmbauche($dateEmbauche);
                    $admin->setRang($faker->randomElement($rang));
                    $admin->setPersonne($personne);
                    $admin->setIsSuper($faker->boolean());
                    $manager->persist($admin);
                }
            }
        }
        for ($i = 0; $i < 20; $i++) {
            if ($this->hasReference('personne_' . $i)) {
                $personne = $this->getReference('personne_' . $i);
                if ($personne->getRole() === 'adherent') {
                    $adherent = new Adherent();
                    $adherent->setPointFidelite(mt_rand(0, 1000));
                    $adherent->setPersonne($personne);
                    $manager->persist($adherent);
                }
            }
        }
        $status = ["actif", "en congé", "retiré"];
        for ($i = 0; $i < 20; $i++) {
            if ($this->hasReference('personne_' . $i)) {
                $personne = $this->getReference('personne_' . $i);
                if ($personne->getRole() === 'technicien') {
                    $adherent = new Technicien();
                    $DateRecrutement = $faker->dateTimeBetween('-7 years', 'now');
                    $adherent->setDateRecrutement($DateRecrutement);
                    $adherent->setStatuts($faker->randomElement($status));
                    $adherent->setPersonne($personne);
                    $manager->persist($adherent);
                }
            }
        }
        $manager->flush();
    }

    // comprendre : Les méthodes getDependencies garantissent que Groupe1Fixtures est chargée avant les autres fixtures.
    public function getDependencies()
    {
        return [
            Groupe1Fixtures::class,
        ];
    }
}
