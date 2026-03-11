<?php

namespace App\Controller;

use App\Form\CvGeneratorType;
use App\Repository\ToolRepository;
use App\Service\CvGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/tool')]
class ToolController extends AbstractController
{
    #[Route('/{routeParam}', name: 'app_tool_show')]
    public function showTool(
        string $routeParam,
        ToolRepository $toolRepository,
        Request $request,
        CvGeneratorService $cvGeneratorService
    ): Response {
        $tool = $toolRepository->findOneBy(['routeParam' => $routeParam]);

        if (!$tool) {
            throw $this->createNotFoundException('This tool does not exist.');
        }

        $viewParameters = ['tool' => $tool];

        if ($tool->getRouteParam() === 'cv-simple') {
            $form = $this->createForm(CvGeneratorType::class);
            $form->handleRequest($request);

            $cvContent = null;
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $cvContent = $cvGeneratorService->generate($data);
            }

            $viewParameters['form'] = $form->createView();
            $viewParameters['cv_content'] = $cvContent;
        }

        return $this->render('tool/index.html.twig', $viewParameters);
    }
}
