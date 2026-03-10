<?php

namespace App\Controller;

use App\Repository\ToolRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/tool')]
class ToolController extends AbstractController
{
    #[Route('/{routeParam}', name: 'app_tool_show')]
    public function showTool(string $routeParam, ToolRepository $toolRepository): Response
    {
        $tool = $toolRepository->findOneBy(['routeParam' => $routeParam]);

        if (!$tool) {
            throw $this->createNotFoundException('This tool does not exist.');
        }

        // Ici, vous pouvez ajouter la logique spécifique pour chaque outil si nécessaire,
        // ou simplement afficher une page générique.
        
        return $this->render('tool/index.html.twig', [
            'tool' => $tool,
        ]);
    }
}
