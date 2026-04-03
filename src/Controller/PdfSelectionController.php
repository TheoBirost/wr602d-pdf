<?php

namespace App\Controller;

use App\Repository\ToolRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfSelectionController extends AbstractController
{
    #[Route('/pdf', name: 'pdf_selection')]
    public function index(ToolRepository $toolRepository): Response
    {
        $tools = $toolRepository->findAll();

        return $this->render('pdf/selection.html.twig', [
            'tools' => $tools,
        ]);
    }
}
