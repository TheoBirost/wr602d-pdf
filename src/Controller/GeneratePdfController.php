<?php

namespace App\Controller;

use App\Service\YourGotenbergService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GeneratePdfController extends AbstractController
{
    private $pdfService;
    private $em;

    public function __construct(YourGotenbergService $pdfService, EntityManagerInterface $em)
    {
        $this->pdfService = $pdfService;
        $this->em = $em;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/pdf/generate/{type}', name: 'pdf_generation', defaults: ['type' => null])]
    public function generatePdf(Request $request, ?string $type): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $plan = $user->getPlan();

        if ($user->getGenerationsUsed() >= $plan->getLimitGeneration()) {
            $this->addFlash('error', 'Vous avez atteint votre limite de générations pour ce mois-ci. Veuillez mettre à niveau votre plan.');
            return $this->redirectToRoute('subscription_change');
        }

        if (!$type) {
            return $this->redirectToRoute('homepage');
        }

        $formBuilder = $this->createFormBuilder(null, [
            'action' => $this->generateUrl('pdf_generation', ['type' => $type]),
            'csrf_protection' => true,
            'csrf_token_id'   => 'pdf_generation',
        ]);

        $validTypes = ['url', 'html_file', 'html_raw', 'docx', 'xlsx', 'image', 'markdown'];
        if (!in_array($type, $validTypes)) {
            return $this->redirectToRoute('homepage');
        }

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
        }

        $formBuilder->add('generate', SubmitType::class, ['label' => 'Générer PDF']);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $pdf = null;

            try {
                // ... (switch case for PDF generation)

                if ($pdf) {
                    // Incrémenter le compteur
                    $user->incrementGenerationsUsed();
                    $this->em->flush();

                    return new Response($pdf, 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="document.pdf"',
                    ]);
                } else {
                    $this->addFlash('error', 'Échec de la génération du PDF.');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
            }
        }

        return $this->render('pdf/_form.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }
}
