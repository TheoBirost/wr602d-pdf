<?php

namespace App\DataFixtures;

use App\Entity\Tool;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ToolFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // On vide la table avant de la remplir pour éviter les doublons
        $manager->createQuery('DELETE FROM App\Entity\Tool')->execute();

        $tools = [
            // Outils existants
            [
                'name' => 'URL vers PDF',
                'description' => 'Convertissez n\'importe quelle page web en un document PDF de haute qualité.',
                'logo' => '<i class="fas fa-link"></i>',
                'routeParam' => 'url-to-pdf',
                'plans' => ['STARTER', 'PRO', 'ELITE', 'LEGEND'],
            ],
            [
                'name' => 'Markdown vers PDF',
                'description' => 'Rédigez en Markdown et obtenez un PDF parfaitement formaté.',
                'logo' => '<i class="fab fa-markdown"></i>',
                'routeParam' => 'markdown-to-pdf',
                'plans' => ['PRO', 'ELITE', 'LEGEND'],
            ],
            [
                'name' => 'Fusionner des PDF',
                'description' => 'Combinez plusieurs documents PDF en un seul fichier.',
                'logo' => '<i class="fas fa-compress-arrows-alt"></i>',
                'routeParam' => 'merge-pdf',
                'plans' => ['ELITE', 'LEGEND'],
            ],
            [
                'name' => 'Diviser un PDF',
                'description' => 'Extrayez une ou plusieurs pages d\'un document PDF.',
                'logo' => '<i class="fas fa-cut"></i>',
                'routeParam' => 'split-pdf',
                'plans' => ['ELITE', 'LEGEND'],
            ],
            // Nouveaux outils
            [
                'name' => 'HTML vers PDF',
                'description' => 'Envoyez un fichier HTML et convertissez-le en PDF.',
                'logo' => '<i class="fab fa-html5"></i>',
                'routeParam' => 'html-to-pdf',
                'plans' => ['PRO', 'ELITE', 'LEGEND'],
            ],
            [
                'name' => 'Screenshot d\'une URL',
                'description' => 'Prenez une capture d\'écran complète d\'une page web.',
                'logo' => '<i class="fas fa-camera"></i>',
                'routeParam' => 'screenshot-url',
                'plans' => ['PRO', 'ELITE', 'LEGEND'],
            ],
            [
                'name' => 'Screenshot d\'un HTML',
                'description' => 'Prenez une capture d\'écran à partir d\'un fichier HTML.',
                'logo' => '<i class="fas fa-file-image"></i>',
                'routeParam' => 'screenshot-html',
                'plans' => ['ELITE', 'LEGEND'],
            ],
            [
                'name' => 'Screenshot de Markdown',
                'description' => 'Prenez une capture d\'écran à partir de texte Markdown.',
                'logo' => '<i class="fas fa-camera-retro"></i>',
                'routeParam' => 'screenshot-markdown',
                'plans' => ['ELITE', 'LEGEND'],
            ],
        ];

        foreach ($tools as $toolData) {
            $tool = new Tool();
            $tool->setName($toolData['name']);
            $tool->setDescription($toolData['description']);
            $tool->setLogo($toolData['logo']);
            $tool->setRouteParam($toolData['routeParam']);
            $tool->setPlans($toolData['plans']);
            $manager->persist($tool);
        }

        $manager->flush();
    }
}
