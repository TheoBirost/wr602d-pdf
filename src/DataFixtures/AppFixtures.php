<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Plan;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $plan = new Plan();
        $plan->setName('STARTER');
        $plan->setDescription('Plan gratuit - Idéal pour tester nos services');
        $plan->setPrice(0);
        $plan->setLimitGeneration(2);
        $plan->setActive(true);
        $plan->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($plan);

        $plan = new Plan();
        $plan->setName('PRO');
        $plan->setDescription('Plan Basic - Pour un usage régulier');
        $plan->setPrice(9);
        $plan->setLimitGeneration(20);
        $plan->setActive(true);
        $plan->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($plan);

        $plan = new Plan();
        $plan->setName('ELITE');
        $plan->setDescription('Plan Premium - Pour les utilisateurs intensifs');
        $plan->setPrice(29);
        $plan->setLimitGeneration(200);
        $plan->setActive(true);
        $plan->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($plan);

        $plan = new Plan();
        $plan->setName('LEGEND');
        $plan->setDescription('Plan Entreprise - Solution complète pour les professionnels');
        $plan->setPrice(99);
        $plan->setLimitGeneration(500);
        $plan->setActive(true);
        $plan->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($plan);

        $manager->flush();
    }
}