<?php

namespace App\Controller;

use App\Service\GotenbergService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeneratePdfController extends AbstractController
{
    public function __construct(private readonly GotenbergService $pdfService)
    {
    }

    #[Route('/generate-pdf', name: 'pdf_generate')]
    public function generatePdf(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('url', UrlType::class, ['required' => true, 'label' => 'URL to convert to PDF'])
            ->add('submit', SubmitType::class, ['label' => 'Generate PDF'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $url = $form->getData()['url'];
            $pdfContent = $this->pdfService->generatePdfFromUrl($url);

            // On affiche la page d'aperçu
            return $this->render('pdf/preview.html.twig', [
                'pdf_data' => base64_encode($pdfContent),
                'original_url' => $url,
            ]);
        }

        // On affiche le formulaire de génération
        return $this->render('pdf/generate_pdf.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/pdf/download', name: 'pdf_download')]
    public function downloadPdf(Request $request): Response
    {
        $url = $request->query->get('url');
        if (!$url) {
            $this->addFlash('error', 'No URL provided for download.');
            return $this->redirectToRoute('pdf_generate');
        }

        $pdfContent = $this->pdfService->generatePdfFromUrl($url);

        // On retourne le PDF pour le téléchargement
        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="generated.pdf"',
        ]);
    }
}
