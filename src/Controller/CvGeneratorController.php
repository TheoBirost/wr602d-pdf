<?php

namespace App\Controller;

use App\Form\CvGeneratorType;
use App\Service\CvGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CvGeneratorController extends AbstractController
{
    #[Route('/cv/generator', name: 'app_cv_generator')]
    public function index(Request $request, CvGeneratorService $cvGeneratorService): Response
    {
        $form = $this->createForm(CvGeneratorType::class);
        $form->handleRequest($request);

        $cvContent = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $cvContent = $cvGeneratorService->generate($data);
        }

        return $this->render('cv_generator/index.html.twig', [
            'form' => $form->createView(),
            'cv_content' => $cvContent,
        ]);
    }
}
