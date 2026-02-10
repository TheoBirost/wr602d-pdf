<?php

namespace App\Service;

use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GotenbergService
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $gotenbergUrl
    ) {
    }

    /**
     * Generates a PDF from an HTML string.
     */
    public function generatePdf(string $html): string
    {
        $formData = new FormDataPart([
            'files' => DataPart::fromPath(sys_get_temp_dir().'/index.html', 'index.html', 'text/html'),
        ]);
        // Create a temporary file to hold the HTML content
        $tmpFilePath = sys_get_temp_dir() . '/'. uniqid('gotenberg_html_', true) . '.html';
        file_put_contents($tmpFilePath, $html);

        $formData = new FormDataPart([
            'files' => DataPart::fromPath($tmpFilePath, 'index.html', 'text/html'),
        ]);

        $response = $this->client->request('POST', $this->gotenbergUrl . '/forms/chromium/convert/html', [
            'headers' => $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToIterable(),
        ]);

        // Clean up the temporary file
        unlink($tmpFilePath);

        return $response->getContent();
    }

    /**
     * Generates a PDF from a public URL.
     */
    public function generatePdfFromUrl(string $url): string
    {
        $formData = new FormDataPart(['url' => $url]);

        $response = $this->client->request('POST', $this->gotenbergUrl . '/forms/chromium/convert/url', [
            'headers' => $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToIterable(),
        ]);

        return $response->getContent();
    }
}
