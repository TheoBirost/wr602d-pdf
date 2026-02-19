<?php

namespace App\Controller;

use App\Service\YourGotenbergService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
        // Explicitly set the action to this controller's route so it works when embedded/AJAX loaded
        $formBuilder = $this->createFormBuilder(null, [
            'action' => $this->generateUrl('pdf_generation', ['type' => $type]),
            'csrf_protection' => true,
            'csrf_token_id'   => 'pdf_generation',
        ]);

        switch ($type) {
            case 'url':
                $formBuilder->add('url', UrlType::class, ['required' => true, 'label' => 'URL']);
                break;
            case 'html_file':
                $formBuilder->add('html_file', FileType::class, ['required' => true, 'label' => 'Fichier HTML']);
                break;
            case 'html_raw':
                $formBuilder->add('html_content', TextareaType::class, ['required' => true, 'label' => 'Contenu HTML Brut', 'attr' => ['rows' => 15]]);
                break;
            case 'docx':
                $formBuilder->add('docx_file', FileType::class, ['required' => true, 'label' => 'Document Word (DOCX)']);
                break;
            case 'xlsx':
                $formBuilder->add('xlsx_file', FileType::class, ['required' => true, 'label' => 'Document Excel (XLSX)']);
                break;
            case 'image':
                $formBuilder->add('image_file', FileType::class, ['required' => true, 'label' => 'Fichier Image (JPG, PNG, etc.)']);
                break;
            case 'markdown':
                $formBuilder->add('markdown_file', FileType::class, ['required' => true, 'label' => 'Fichier Markdown (MD)']);
                break;
            default:
                return $this->redirectToRoute('homepage');
        }

        $formBuilder->add('generate', SubmitType::class, ['label' => 'Générer PDF']);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $pdf = null;

            try {
                switch ($type) {
                    case 'url':
                        $pdf = $this->pdfService->generatePdfFromUrl($data['url']);
                        break;
                    case 'html_file':
                        $pdf = $this->pdfService->generatePdfFromHtml(null, $form->get('html_file')->getData());
                        break;
                    case 'html_raw':
                        $pdf = $this->pdfService->generatePdfFromHtml($data['html_content']);
                        break;
                    case 'docx':
                        $pdf = $this->pdfService->generatePdfFromOffice($form->get('docx_file')->getData());
                        break;
                    case 'xlsx':
                        $pdf = $this->pdfService->generatePdfFromOffice($form->get('xlsx_file')->getData());
                        break;
                    case 'image':
                        $pdf = $this->pdfService->generatePdfFromImage($form->get('image_file')->getData());
                        break;
                    case 'markdown':
                        $pdf = $this->pdfService->generatePdfFromMarkdown($form->get('markdown_file')->getData());
                        break;
                }

                if ($pdf) {
                    return new Response($pdf, 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="document.pdf"',
                    ]);
                } else {
                    $this->addFlash('error', 'Échec de la génération du PDF. Le service n\'a renvoyé aucune donnée.');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
            }
        }

        // If it's an AJAX request (or we want to embed it), render just the form partial
        // We check if the form is submitted and invalid, or just a GET request
        $template = $request->isXmlHttpRequest() ? 'pdf/_form.html.twig' : 'pdf/generate.html.twig';

        // If the form was submitted but invalid (and it's not AJAX), we might want to stay on the page.
        // But since we are moving to a SPA-like feel on the homepage, the AJAX handling in JS will take care of the response.

        return $this->render($template, [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }
}
