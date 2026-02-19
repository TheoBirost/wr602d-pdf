<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfSelectionController extends AbstractController
{
    #[Route('/pdf', name: 'pdf_selection')]
    public function index(): Response
    {
        $converters = [
            [
                'type' => 'url',
                'name' => 'URL to PDF',
                'description' => 'Convert a web page directly from its URL to a PDF document.',
            ],
            [
                'type' => 'html_file',
                'name' => 'HTML File to PDF',
                'description' => 'Upload an HTML file and convert it into a PDF document.',
            ],
            [
                'type' => 'html_raw',
                'name' => 'Raw HTML to PDF',
                'description' => 'Paste raw HTML content directly to generate a PDF document.',
            ],
            [
                'type' => 'docx',
                'name' => 'Word to PDF',
                'description' => 'Transform Microsoft Word documents (.docx) into high-quality PDF files.',
            ],
            [
                'type' => 'xlsx',
                'name' => 'Excel to PDF',
                'description' => 'Convert Microsoft Excel spreadsheets (.xlsx) into PDF documents, preserving formatting.',
            ],
            [
                'type' => 'image',
                'name' => 'Image to PDF',
                'description' => 'Convert various image formats (JPG, PNG, GIF, etc.) into a single PDF document.',
            ],
            [
                'type' => 'markdown',
                'name' => 'Markdown to PDF',
                'description' => 'Convert Markdown files (.md) into elegantly formatted PDF documents.',
            ],
        ];

        return $this->render('pdf/selection.html.twig', [
            'converters' => $converters,
        ]);
    }
}
