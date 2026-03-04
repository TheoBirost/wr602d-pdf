<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/tool')]
class ToolController extends AbstractController
{
    #[Route('/cv-simple', name: 'app_tool_cv_simple')]
    public function cvSimple(): Response
    {
        return $this->render('tool/index.html.twig', [
            'tool_name' => 'Générateur de CV Simple',
        ]);
    }

    #[Route('/cover-letter', name: 'app_tool_cover_letter')]
    public function coverLetter(): Response
    {
        return $this->render('tool/index.html.twig', [
            'tool_name' => 'Générateur de Lettre de Motivation',
        ]);
    }

    #[Route('/cv-design', name: 'app_tool_cv_design')]
    public function cvDesign(): Response
    {
        return $this->render('tool/index.html.twig', [
            'tool_name' => 'Générateur de CV Design',
        ]);
    }

    #[Route('/annual-report', name: 'app_tool_annual_report')]
    public function annualReport(): Response
    {
        return $this->render('tool/index.html.twig', [
            'tool_name' => 'Rapport Annuel Automatisé',
        ]);
    }
}