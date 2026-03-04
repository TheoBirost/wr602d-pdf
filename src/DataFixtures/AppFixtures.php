<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Plan;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $plansData = [
            [
                'name' => 'STARTER',
                'description' => 'Plan gratuit - Idéal pour tester nos services',
                'price' => 0,
                'limit' => 2,
            ],
            [
                'name' => 'PRO',
                'description' => 'Plan Basic - Pour un usage régulier',
                'price' => 9,
                'limit' => 20,
            ],
            [
                'name' => 'ELITE',
                'description' => 'Plan Premium - Pour les utilisateurs intensifs',
                'price' => 29,
                'limit' => 200,
            ],
            [
                'name' => 'LEGEND',
                'description' => 'Plan Entreprise - Solution complète pour les professionnels',
                'price' => 99,
                'limit' => 500,
            ],
        ];

        $plans = [];
        foreach ($plansData as $planData) {
            $plan = new Plan();
            $plan->setName($planData['name']);
            $plan->setDescription($planData['description']);
            $plan->setPrice($planData['price']);
            $plan->setLimitGeneration($planData['limit']);
            $plan->setActive(true);
            $manager->persist($plan);
            $plans[$planData['name']] = $plan;
        }

        $manager->flush();

        // Create a default user with the STARTER plan
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $user->setRoles(['ROLE_USER']);
        $user->setPlan($plans['STARTER']);
        $user->setIsVerified(true);
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);
        $manager->persist($user);

        $manager->flush();
    }
}
