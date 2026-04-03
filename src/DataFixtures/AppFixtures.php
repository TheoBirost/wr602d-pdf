<?php

namespace App\DataFixtures;

use App\Entity\Tool;
use App\Entity\Plan;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // --- 1. GESTION DES PLANS ---
        $planData = [
            'STARTER' => ['price' => 0, 'limit' => 5, 'description' => 'Idéal pour découvrir et pour les besoins occasionnels.', 'stripePriceId' => 'price_1T9LTlBbxRtKe5SmHC7ugkFO'],
            'PRO' => ['price' => 19, 'limit' => 50, 'description' => 'Parfait pour les professionnels et les usages réguliers.', 'stripePriceId' => 'price_1T9LU5BbxRtKe5SmDViJXp3e'],
            'ELITE' => ['price' => 49, 'limit' => 200, 'description' => 'La solution complète pour les utilisateurs intensifs.', 'stripePriceId' => 'price_1T9LUaBbxRtKe5SmYXFCBoZS'],
            'LEGEND' => ['price' => 99, 'limit' => -1, 'description' => 'Accès total et sans limites pour une puissance maximale.', 'stripePriceId' => 'price_1T9LUuBbxRtKe5SmmmmMlmog'],
        ];

        foreach ($planData as $name => $data) {
            $plan = $manager->getRepository(Plan::class)->findOneBy(['name' => $name]) ?? new Plan();
            $plan->setName($name);
            $plan->setPrice($data['price']);
            $plan->setLimitGeneration($data['limit']);
            $plan->setDescription($data['description']);
            $plan->setActive(true);
            $manager->persist($plan);
        }
        $manager->flush();

        // --- 2. GESTION DES OUTILS ---
        $toolsData = [
            ['name' => 'URL vers PDF', 'route' => 'url-to-pdf', 'logo' => '<i class="fas fa-link"></i>', 'plan' => 'STARTER', 'description' => 'Convertissez une page web en PDF.'],
            ['name' => 'HTML vers PDF', 'route' => 'html-to-pdf', 'logo' => '<i class="fab fa-html5"></i>', 'plan' => 'PRO', 'description' => 'Transformez un fichier HTML en PDF.'],
            ['name' => 'Markdown vers PDF', 'route' => 'markdown-to-pdf', 'logo' => '<i class="fab fa-markdown"></i>', 'plan' => 'PRO', 'description' => 'Convertissez vos fichiers Markdown en PDF.'],
            ['name' => 'Documents Office vers PDF', 'route' => 'office-to-pdf', 'logo' => '<i class="fas fa-file-word"></i>', 'plan' => 'PRO', 'description' => 'Convertissez des documents Office en PDF.'],
            ['name' => 'Fusionner des PDF', 'route' => 'merge-pdf', 'logo' => '<i class="fas fa-compress-arrows-alt"></i>', 'plan' => 'ELITE', 'description' => 'Fusionnez plusieurs fichiers PDF en un seul.'],
            ['name' => 'Diviser un PDF', 'route' => 'split-pdf', 'logo' => '<i class="fas fa-cut"></i>', 'plan' => 'ELITE', 'description' => 'Extrayez une ou plusieurs pages d\'un PDF.'],
            ['name' => 'Screenshot d\'une URL', 'route' => 'screenshot-url', 'logo' => '<i class="fas fa-camera"></i>', 'plan' => 'PRO', 'description' => 'Prenez une capture d\'écran d\'une page web.'],
            ['name' => 'Screenshot de HTML', 'route' => 'screenshot-html', 'logo' => '<i class="fas fa-file-image"></i>', 'plan' => 'ELITE', 'description' => 'Prenez une capture d\'écran d\'un fichier HTML.'],
            ['name' => 'Screenshot de Markdown', 'route' => 'screenshot-markdown', 'logo' => '<i class="fas fa-camera-retro"></i>', 'plan' => 'ELITE', 'description' => 'Prenez une capture d\'écran de texte Markdown.'],
        ];

        $planHierarchy = ['STARTER', 'PRO', 'ELITE', 'LEGEND'];

        // Supprimer les outils qui ne sont plus dans la liste
        $allTools = $manager->getRepository(Tool::class)->findAll();
        $toolsToKeep = array_column($toolsData, 'route');
        foreach ($allTools as $tool) {
            if (!in_array($tool->getRouteParam(), $toolsToKeep)) {
                $manager->remove($tool);
            }
        }

        foreach ($toolsData as $toolData) {
            $tool = $manager->getRepository(Tool::class)->findOneBy(['routeParam' => $toolData['route']]) ?? new Tool();
            
            $tool->setName($toolData['name']);
            $tool->setRouteParam($toolData['route']);
            $tool->setDescription($toolData['description']);
            $tool->setLogo($toolData['logo']);

            $requiredPlanIndex = array_search($toolData['plan'], $planHierarchy);
            $accessiblePlans = [];
            if ($requiredPlanIndex !== false) {
                for ($i = $requiredPlanIndex; $i < count($planHierarchy); $i++) {
                    $accessiblePlans[] = $planHierarchy[$i];
                }
            }
            $tool->setPlans($accessiblePlans);

            $manager->persist($tool);
        }

        $manager->flush();
    }
}
