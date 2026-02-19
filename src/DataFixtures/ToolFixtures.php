<?php

namespace App\DataFixtures;

use App\Entity\Tool;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ToolFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tools = [
            [
                'name' => 'URL vers PDF',
                'description' => 'Convertissez n\'importe quelle page web en document PDF.',
                'logo' => '<i class="fa-solid fa-link"></i>',
                'plans' => ['starter', 'pro', 'elite', 'legend'],
                'route_param' => 'url',
            ],
            [
                'name' => 'HTML brut vers PDF',
                'description' => 'Transformez votre code HTML en un fichier PDF.',
                'logo' => '<i class="fa-solid fa-code"></i>',
                'plans' => ['starter', 'pro', 'elite', 'legend'],
                'route_param' => 'html_raw',
            ],
            [
                'name' => 'Fichier HTML vers PDF',
                'description' => 'Chargez un fichier HTML pour le convertir en PDF.',
                'logo' => '<i class="fa-brands fa-html5"></i>',
                'plans' => ['pro', 'elite', 'legend'],
                'route_param' => 'html_file',
            ],
            [
                'name' => 'DOCX vers PDF',
                'description' => 'Convertissez vos documents Word en PDF.',
                'logo' => '<i class="fa-solid fa-file-word"></i>',
                'plans' => ['pro', 'elite', 'legend'],
                'route_param' => 'docx',
            ],
            [
                'name' => 'XLSX vers PDF',
                'description' => 'Convertissez vos feuilles de calcul Excel en PDF.',
                'logo' => '<i class="fa-solid fa-file-excel"></i>',
                'plans' => ['elite', 'legend'],
                'route_param' => 'xlsx',
            ],
            [
                'name' => 'Image vers PDF',
                'description' => 'Fusionnez une ou plusieurs images dans un seul fichier PDF.',
                'logo' => '<i class="fa-solid fa-file-image"></i>',
                'plans' => ['legend'],
                'route_param' => 'image',
            ],
            [
                'name' => 'Markdown vers PDF',
                'description' => 'Convertissez vos fichiers Markdown en PDF stylisés.',
                'logo' => '<i class="fa-brands fa-markdown"></i>',
                'plans' => ['elite', 'legend'],
                'route_param' => 'markdown',
            ],
        ];

        foreach ($tools as $toolData) {
            $tool = new Tool();
            $tool->setName($toolData['name']);
            $tool->setDescription($toolData['description']);
            $tool->setLogo($toolData['logo']);
            $tool->setPlans($toolData['plans']);
            $tool->setRouteParam($toolData['route_param']);
            $manager->persist($tool);
        }

        $manager->flush();
    }
}
