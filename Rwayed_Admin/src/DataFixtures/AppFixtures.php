<?php

namespace App\DataFixtures;

use App\Factory\AdherentFactory;
use App\Factory\AdminFactory;
use App\Factory\TechnicienFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        AdherentFactory::createMany(30);
        AdminFactory::createMany(5);
        TechnicienFactory::createMany(20);

    }
}
