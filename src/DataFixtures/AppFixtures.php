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
        $planData = [
            'STARTER' => ['price' => 0, 'limit' => 2, 'description' => 'Pour bien démarrer','stripePriceId' => 'price_1T9LTlBbxRtKe5SmHC7ugkFO'],
            'PRO' => ['price' => 19, 'limit' => 25, 'description' => 'Pour les professionnels','stripePriceId' => 'price_1T9LU5BbxRtKe5SmDViJXp3e'],
            'ELITE' => ['price' => 49, 'limit' => 50, 'description' => 'Pour les utilisateurs exigeants','stripePriceId' => 'price_1T9LUaBbxRtKe5SmYXFCBoZS'],
            'LEGEND' => ['price' => 99, 'limit' => 200, 'description' => 'Accès total et illimité','stripePriceId' => 'price_1T9LUuBbxRtKe5SmmmmMlmog'],
        ];

        foreach ($planData as $name => $data) {
            $plan = $manager->getRepository(Plan::class)->findOneBy(['name' => $name]);
            if (!$plan) {
                $plan = new Plan();
                $plan->setName($name);
                $plan->setPrice($data['price']);
                $plan->setLimitGeneration($data['limit']);
                $plan->setDescription($data['description']);
                $plan->setActive(true);
                $manager->persist($plan);
            }
        }
        $manager->flush();

        // --- 2. GESTION DES OUTILS ---
        // Liste complète de TOUS les outils que vous voulez
        $toolsData = [
            // Anciens outils
            ['name' => 'Générateur de CV Simple', 'route' => 'cv-simple', 'logo' => '<i class="fa-solid fa-id-card"></i>', 'plan' => 'STARTER', 'description' => 'Créez un CV simple et efficace.'],
            ['name' => 'Générateur de Lettre de Motivation', 'route' => 'cover-letter', 'logo' => '<i class="fa-solid fa-file-signature"></i>', 'plan' => 'STARTER', 'description' => 'Générez une lettre de motivation percutante.'],
            ['name' => 'Générateur de CV Design', 'route' => 'cv-design', 'logo' => '<i class="fa-solid fa-palette"></i>', 'plan' => 'PRO', 'description' => 'Concevez un CV au design moderne.'],
            ['name' => 'Rapport Annuel Automatisé', 'route' => 'annual-report', 'logo' => '<i class="fa-solid fa-chart-line"></i>', 'plan' => 'ELITE', 'description' => 'Automatisez la création de vos rapports annuels.'],
            
            // Outils Gotenberg
            ['name' => 'URL to PDF', 'route' => 'url', 'logo' => '<i class="fa-solid fa-link"></i>', 'plan' => 'STARTER', 'description' => 'Convertissez une page web en PDF.'],
            ['name' => 'HTML to PDF', 'route' => 'html_raw', 'logo' => '<i class="fa-solid fa-code"></i>', 'plan' => 'STARTER', 'description' => 'Transformez du code HTML en PDF.'],
            ['name' => 'Markdown to PDF', 'route' => 'markdown', 'logo' => '<i class="fa-brands fa-markdown"></i>', 'plan' => 'PRO', 'description' => 'Convertissez vos fichiers Markdown en PDF.'],
            ['name' => 'Office to PDF', 'route' => 'docx', 'logo' => '<i class="fa-solid fa-file-word"></i>', 'plan' => 'PRO', 'description' => 'Convertissez des documents Office (Word, Excel) en PDF.'],
            ['name' => 'Merge PDF', 'route' => 'merge', 'logo' => '<i class="fa-solid fa-object-group"></i>', 'plan' => 'ELITE', 'description' => 'Fusionnez plusieurs fichiers PDF en un seul.'],
            ['name' => 'Screenshot Page', 'route' => 'screenshot', 'logo' => '<i class="fa-solid fa-camera"></i>', 'plan' => 'ELITE', 'description' => 'Prenez une capture d\'écran d\'une page web.'],
        ];

        $planHierarchy = ['STARTER', 'PRO', 'ELITE', 'LEGEND'];

        foreach ($toolsData as $toolData) {
            $tool = $manager->getRepository(Tool::class)->findOneBy(['routeParam' => $toolData['route']]);
            
            if (!$tool) {
                $tool = new Tool();
                $tool->setName($toolData['name']);
                $tool->setRouteParam($toolData['route']);
            }
            
            // On met à jour la description, le logo et les plans dans tous les cas
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
