<?php

namespace App\Controller;

use App\Service\YourGotenbergService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class GeneratePdfController extends AbstractController
{
    private $pdfService;

    public function __construct(YourGotenbergService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    #[Route('/pdf/generate/{type}', name: 'pdf_generation', defaults: ['type' => null])]
    public function generatePdf(Request $request, ?string $type): Response
    {
        $formBuilder = $this->createFormBuilder();

        if ($type === 'url') {
            $formBuilder->add('url', UrlType::class, ['required' => true]);
        } elseif ($type === 'html') {
            $formBuilder->add('html_file', FileType::class, ['required' => true]);
        }

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (isset($data['url'])) {
                // $pdf = $this->pdfService->generatePdfFromUrl($data['url']);
                // Handle the PDF response, e.g., force download
            } elseif (isset($data['html_file'])) {
                // $pdf = $this->pdfService->generatePdfFromFile($data['html_file']);
                // Handle the PDF response
            }

            // For now, just redirect to home
            return $this->redirectToRoute('homepage');
        }

        $template = $request->isXmlHttpRequest() ? 'pdf/_form.html.twig' : 'pdf/generate.html.twig';

        return $this->render($template, [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }
}
