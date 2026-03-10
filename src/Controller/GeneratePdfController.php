<?php

namespace App\Controller;

use App\Entity\Generation;
use App\Repository\ToolRepository;
use App\Security\Voter\ToolVoter;
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
use Symfony\Component\String\Slugger\SluggerInterface;

class GeneratePdfController extends AbstractController
{
    private $pdfService;
    private $em;
    private $slugger;

    public function __construct(YourGotenbergService $pdfService, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $this->pdfService = $pdfService;
        $this->em = $em;
        $this->slugger = $slugger;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/pdf/generate/{type}', name: 'pdf_generation', defaults: ['type' => null])]
    public function generatePdf(Request $request, ?string $type, ToolRepository $toolRepository): Response
    {
        if (!$type) {
            return $this->redirectToRoute('homepage');
        }

        $tool = $toolRepository->findOneBy(['routeParam' => $type]);

        if (!$tool) {
            throw $this->createNotFoundException('This tool does not exist.');
        }

        if (!$this->isGranted(ToolVoter::VIEW, $tool)) {
            $this->addFlash('error', 'Your current plan does not grant access to this tool. Please upgrade your plan.');
            return $this->redirectToRoute('subscription_change');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $plan = $user->getPlan();

        if ($plan && $user->getGenerationsUsed() >= $plan->getLimitGeneration()) {
            $this->addFlash('error', 'You have reached your generation limit for this month. Please upgrade your plan.');
            return $this->redirectToRoute('subscription_change');
        }

        $formBuilder = $this->createFormBuilder(null, [
            'action' => $this->generateUrl('pdf_generation', ['type' => $type]),
            'csrf_protection' => true,
            'csrf_token_id'   => 'pdf_generation',
        ]);

        $validTypes = ['url', 'html_file', 'html_raw', 'docx', 'xlsx', 'image', 'markdown', 'merge', 'screenshot'];
        if (!in_array($type, $validTypes)) {
            return $this->redirectToRoute('homepage');
        }

        switch ($type) {
            case 'url':
                $formBuilder->add('from_url', UrlType::class);
                break;
            case 'html_file':
                $formBuilder->add('html_file', FileType::class);
                break;
            case 'html_raw':
                $formBuilder->add('html_content', TextareaType::class);
                break;
            case 'docx':
                $formBuilder->add('docx_file', FileType::class);
                break;
            case 'xlsx':
                $formBuilder->add('xlsx_file', FileType::class);
                break;
            case 'image':
                $formBuilder->add('image_file', FileType::class);
                break;
            case 'markdown':
                $formBuilder->add('markdown_file', FileType::class);
                break;
        }

        $formBuilder->add('generate', SubmitType::class, ['label' => 'Générer']);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $result = null;
            $originalFilename = 'document';
            $extension = 'pdf';

            try {
                switch ($type) {
                    case 'url':
                        $result = $this->pdfService->generatePdfFromUrl($data['from_url']);
                        $originalFilename = 'generated-from-url';
                        break;
                    case 'html_file':
                        $file = $data['html_file'];
                        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $result = $this->pdfService->generatePdfFromHtml(file_get_contents($file->getPathname()));
                        break;
                    case 'html_raw':
                        $result = $this->pdfService->generatePdfFromHtml($data['html_content']);
                        $originalFilename = 'generated-from-html';
                        break;
                    // ... other cases
                }

                if ($result) {
                    // --- LOGIQUE D'ENREGISTREMENT ---
                    $safeFilename = $this->slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$extension;
                    $targetDirectory = $this->getParameter('kernel.project_dir').'/public/uploads/generations';
                    
                    // Ensure the directory exists
                    if (!is_dir($targetDirectory)) {
                        mkdir($targetDirectory, 0777, true);
                    }

                    file_put_contents($targetDirectory.'/'.$newFilename, $result);

                    // Create a new Generation entity
                    $generation = new Generation();
                    $generation->setUser($user);
                    $generation->setToolName($tool->getName());
                    $generation->setFilePath('uploads/generations/'.$newFilename);
                    
                    $this->em->persist($generation);
                    // --- FIN DE LA LOGIQUE ---

                    $user->incrementGenerationsUsed();
                    $this->em->flush();
                    
                    return $this->file($targetDirectory.'/'.$newFilename, $originalFilename.'.'.$extension);
                } else {
                    $this->addFlash('error', 'Failed to generate the file.');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
            }
        }

        return $this->render('pdf/generate.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
            'tool' => $tool,
        ]);
    }
}
