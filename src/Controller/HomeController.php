<?php
// src/Controller/HomeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    public function index(): Response
    {
        $tools = [
            [
                'routeParam' => 'pdf-to-word',
                'plans' => ['starter', 'pro', 'elite', 'legend'],
                'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text pdf-option-icon"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>',
                'name' => 'PDF en Word',
                'description' => 'Convertissez vos documents PDF en fichiers Word modifiables.'
            ],
            [
                'routeParam' => 'word-to-pdf',
                'plans' => ['starter', 'pro', 'elite', 'legend'],
                'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text pdf-option-icon"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>',
                'name' => 'Word en PDF',
                'description' => 'Transformez vos documents Word en fichiers PDF de haute qualité.'
            ],
            [
                'routeParam' => 'pdf-to-excel',
                'plans' => ['pro', 'elite', 'legend'],
                'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-spreadsheet pdf-option-icon"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M8 21h8"/><path d="M8 17h8"/><path d="M8 13h8"/><path d="M8 9h8"/></svg>',
                'name' => 'PDF en Excel',
                'description' => 'Extrayez des données de PDF vers des feuilles de calcul Excel.'
            ],
            [
                'routeParam' => 'excel-to-pdf',
                'plans' => ['pro', 'elite', 'legend'],
                'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-spreadsheet pdf-option-icon"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M8 21h8"/><path d="M8 17h8"/><path d="M8 13h8"/><path d="M8 9h8"/></svg>',
                'name' => 'Excel en PDF',
                'description' => 'Convertissez vos fichiers Excel en documents PDF.'
            ],
            [
                'routeParam' => 'pdf-to-jpg',
                'plans' => ['starter', 'pro', 'elite', 'legend'],
                'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-image pdf-option-icon"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><circle cx="10" cy="12" r="2"/><path d="m20 17-1.29-1.29a1 1 0 0 0-1.32-.08L9 22H6a2 2 0 0 1-2-2v-2"/></svg>',
                'name' => 'PDF en JPG',
                'description' => 'Convertissez chaque page PDF en une image JPG.'
            ],
            [
                'routeParam' => 'jpg-to-pdf',
                'plans' => ['starter', 'pro', 'elite', 'legend'],
                'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-image pdf-option-icon"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><circle cx="10" cy="12" r="2"/><path d="m20 17-1.29-1.29a1 1 0 0 0-1.32-.08L9 22H6a2 2 0 0 1-2-2v-2"/></svg>',
                'name' => 'JPG en PDF',
                'description' => 'Convertissez des images JPG en un seul fichier PDF.'
            ],
            [
                'routeParam' => 'merge-pdf',
                'plans' => ['starter', 'pro', 'elite', 'legend'],
                'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-files pdf-option-icon"><path d="M20 7V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h2"/><path d="M10 9h6a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-6a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2Z"/></svg>',
                'name' => 'Fusionner PDF',
                'description' => 'Combinez plusieurs fichiers PDF en un seul document.'
            ],
            [
                'routeParam' => 'split-pdf',
                'plans' => ['pro', 'elite', 'legend'],
                'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-minus pdf-option-icon"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M9 15h6"/></svg>',
                'name' => 'Diviser PDF',
                'description' => 'Divisez un PDF en plusieurs fichiers ou extrayez des pages spécifiques.'
            ],
            [
                'routeParam' => 'compress-pdf',
                'plans' => ['starter', 'pro', 'elite', 'legend'],
                'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-compression pdf-option-icon"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 13V9h-2"/><path d="M10 13h4"/><path d="M10 13l-2 2"/><path d="M12 17v-4h2"/><path d="M12 17h-4"/><path d="M12 17l2 2"/></svg>',
                'name' => 'Compresser PDF',
                'description' => 'Réduisez la taille de vos fichiers PDF sans perte de qualité.'
            ],
            [
                'routeParam' => 'rotate-pdf',
                'plans' => ['pro', 'elite', 'legend'],
                'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-rotate-cw pdf-option-icon"><path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>',
                'name' => 'Rotation PDF',
                'description' => 'Faites pivoter des pages spécifiques ou toutes les pages d\'un PDF.'
            ],
            [
                'routeParam' => 'pdf-to-powerpoint',
                'plans' => ['elite', 'legend'],
                'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-bar-chart pdf-option-icon"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M8 18v-4"/><path d="M12 18v-2"/><path d="M16 18v-6"/></svg>',
                'name' => 'PDF en PowerPoint',
                'description' => 'Convertissez vos PDF en présentations PowerPoint modifiables.'
            ],
            [
                'routeParam' => 'powerpoint-to-pdf',
                'plans' => ['elite', 'legend'],
                'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-bar-chart pdf-option-icon"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M8 18v-4"/><path d="M12 18v-2"/><path d="M16 18v-6"/></svg>',
                'name' => 'PowerPoint en PDF',
                'description' => 'Transformez vos présentations PowerPoint en fichiers PDF.'
            ]
        ];

        //-- le fichier sera donc dans templates/home/index.html.twig
        return $this->render('home/index.html.twig', [
            'tools' => $tools,
        ]);
    }
}
