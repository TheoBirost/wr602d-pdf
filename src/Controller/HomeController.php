<?php
// src/Controller/HomeController.php

namespace App\Controller;

use App\Repository\ToolRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    public function index(ToolRepository $toolRepository): Response
    {
        $tools = $toolRepository->findAll();

        // Définir l'ordre de tri souhaité pour les plans
        $planOrder = ['STARTER', 'PRO', 'ELITE', 'LEGEND'];

        // Trier les outils en fonction de l'ordre des plans
        usort($tools, function ($a, $b) use ($planOrder) {
            // Récupérer le plan minimum pour chaque outil (le premier du tableau 'plans')
            $planA = !empty($a->getPlans()) ? strtoupper($a->getPlans()[0]) : null;
            $planB = !empty($b->getPlans()) ? strtoupper($b->getPlans()[0]) : null;

            // Trouver l'index de chaque plan dans le tableau de tri
            $indexA = $planA ? array_search($planA, $planOrder) : PHP_INT_MAX;
            $indexB = $planB ? array_search($planB, $planOrder) : PHP_INT_MAX;
            
            // Si un plan n'est pas trouvé, le mettre à la fin
            if ($indexA === false) $indexA = PHP_INT_MAX;
            if ($indexB === false) $indexB = PHP_INT_MAX;

            // Comparer les index pour le tri
            return $indexA <=> $indexB;
        });

        //-- le fichier sera donc dans templates/home/index.html.twig
        return $this->render('home/index.html.twig', [
            'tools' => $tools,
        ]);
    }
}
