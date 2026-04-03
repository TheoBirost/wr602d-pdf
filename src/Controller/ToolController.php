<?php

namespace App\Controller;

use App\Entity\Generation;
use App\Form\HtmlToPdfType;
use App\Form\MarkdownToPdfType;
use App\Form\MergePdfType;
use App\Form\OfficeToPdfType;
use App\Form\ScreenshotHtmlType;
use App\Form\ScreenshotMarkdownType;
use App\Form\ScreenshotUrlType;
use App\Form\SplitPdfType;
use App\Form\UrlToPdfType;
use App\Repository\ToolRepository;
use App\Service\YourGotenbergService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/tool')]
class ToolController extends AbstractController
{
    #[Route('/{routeParam}', name: 'app_tool_show')]
    public function showTool(
        string $routeParam,
        ToolRepository $toolRepository,
        Request $request,
        YourGotenbergService $gotenbergService,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $tool = $toolRepository->findOneBy(['routeParam' => $routeParam]);

        if (!$tool) {
            throw $this->createNotFoundException('This tool does not exist.');
        }

        if (!in_array('STARTER', $tool->getPlans())) {
            $this->denyAccessUnlessGranted('ROLE_USER');
        }

        $viewParameters = ['tool' => $tool];
        $form = null;
        
        $formTypes = [
            'url-to-pdf' => UrlToPdfType::class,
            'markdown-to-pdf' => MarkdownToPdfType::class,
            'office-to-pdf' => OfficeToPdfType::class,
            'merge-pdf' => MergePdfType::class,
            'split-pdf' => SplitPdfType::class,
            'html-to-pdf' => HtmlToPdfType::class,
            'screenshot-url' => ScreenshotUrlType::class,
            'screenshot-html' => ScreenshotHtmlType::class,
            'screenshot-markdown' => ScreenshotMarkdownType::class,
        ];

        if (array_key_exists($tool->getRouteParam(), $formTypes)) {
            $formType = $formTypes[$tool->getRouteParam()];
            $form = $this->createForm($formType);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $content = null;
                $isScreenshot = false;
                $originalFilename = 'document';

                switch ($tool->getRouteParam()) {
                    case 'url-to-pdf':
                        $content = $gotenbergService->generatePdfFromUrl($form->get('url')->getData());
                        $originalFilename = 'generated-from-url';
                        break;
                    case 'markdown-to-pdf':
                        $file = $form->get('file')->getData();
                        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $content = $gotenbergService->generatePdfFromMarkdown($file);
                        break;
                    case 'office-to-pdf':
                        $file = $form->get('file')->getData();
                        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $content = $gotenbergService->generatePdfFromOffice($file);
                        break;
                    case 'html-to-pdf':
                        $file = $form->get('file')->getData();
                        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $content = $gotenbergService->generatePdfFromHtml(null, $file);
                        break;
                    case 'merge-pdf':
                        $content = $gotenbergService->mergePdfs($form->get('files')->getData());
                        $originalFilename = 'merged-pdf';
                        break;
                    case 'split-pdf':
                        // The splitPdf method is not defined in YourGotenbergService.
                        break;
                    case 'screenshot-url':
                        $content = $gotenbergService->generateScreenshotFromUrl($form->get('url')->getData());
                        $originalFilename = 'screenshot-from-url';
                        $isScreenshot = true;
                        break;
                    case 'screenshot-html':
                        // The screenshotHtml method is not defined in YourGotenbergService.
                        $isScreenshot = true;
                        break;
                    case 'screenshot-markdown':
                        // The screenshotMarkdown method is not defined in YourGotenbergService.
                        $isScreenshot = true;
                        break;
                }

                if ($content) {
                    /** @var \App\Entity\User $user */
                    $user = $this->getUser();
                    $extension = $isScreenshot ? 'png' : 'pdf';
                    
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$extension;
                    $targetDirectory = $this->getParameter('kernel.project_dir').'/public/uploads/generations';

                    if (!is_dir($targetDirectory)) {
                        mkdir($targetDirectory, 0777, true);
                    }

                    file_put_contents($targetDirectory.'/'.$newFilename, $content);

                    $generation = new Generation();
                    $generation->setUser($user);
                    $generation->setToolName($tool->getName());
                    $generation->setFilePath('uploads/generations/'.$newFilename);
                    
                    $em->persist($generation);
                    
                    if ($user->getPlan()->getLimitGeneration() != -1) {
                        $user->incrementGenerationsUsed();
                    }
                    
                    $em->flush();

                    return $this->file($targetDirectory.'/'.$newFilename, $originalFilename.'.'.$extension);
                }
                $this->addFlash('error', 'La conversion a échoué.');
            }
            $viewParameters['form'] = $form->createView();
        }

        return $this->render('tool/index.html.twig', $viewParameters);
    }
}
