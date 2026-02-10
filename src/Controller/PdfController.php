<?php

namespace App\Controller;

use App\Service\GotenbergService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
    private GotenbergService $gotenbergService;

    public function __construct(GotenbergService $gotenbergService)
    {
        $this->gotenbergService = $gotenbergService;
    }

    #[Route('/pdf/generate', name: 'app_pdf_generate')]
    public function generatePdf(): Response
    {
        $html = '<h1>Hello, PDF!</h1>';
        $pdfContent = $this->gotenbergService->generatePdf($html);

        return new Response(
            $pdfContent,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="generated.pdf"',
            ]
        );
    }
}
