<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class YourGotenbergService
{
    private $httpClient;
    private $gotenbergBaseUrl;
    private $logger;

    public function __construct(HttpClientInterface $httpClient, string $gotenbergBaseUrl, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->gotenbergBaseUrl = $gotenbergBaseUrl;
        $this->logger = $logger;
    }

    private function sendGotenbergRequest(string $endpoint, array $files = [], array $formFields = []): ?string
    {
        try {
            $formFieldsData = $formFields;
            
            foreach ($files as $name => $file) {
                if ($file instanceof UploadedFile) {
                    $filename = $file->getClientOriginalName();
                    if ($name === 'index.html') {
                        $filename = 'index.html';
                        $name = 'files'; 
                    }
                    $formFieldsData[$name] = DataPart::fromPath($file->getRealPath(), $filename);
                } elseif (is_string($file)) { 
                    $filename = 'index.html';
                    if ($name === 'index.html') {
                        $name = 'files';
                    }
                    $formFieldsData[$name] = new DataPart($file, $filename, 'text/html');
                }
            }

            $formData = new FormDataPart($formFieldsData);
            $headers = $formData->getPreparedHeaders()->toArray();

            $response = $this->httpClient->request('POST', $this->gotenbergBaseUrl . $endpoint, [
                'headers' => $headers,
                'body' => $formData->bodyToIterable(),
            ]);

            if ($response->getStatusCode() === 200) {
                return $response->getContent();
            } else {
                $this->logger->error('Gotenberg API error: ' . $response->getStatusCode() . ' - ' . $response->getContent(false));
                return null;
            }
        } catch (\Exception $e) {
            $this->logger->error('Error connecting to Gotenberg: ' . $e->getMessage());
            return null;
        }
    }

    public function generatePdfFromUrl(string $url): ?string
    {
        return $this->sendGotenbergRequest('/forms/chromium/convert/url', [], ['url' => $url]);
    }

    public function generatePdfFromHtml(?string $htmlContent, ?UploadedFile $htmlFile = null): ?string
    {
        $files = [];
        if ($htmlFile) {
            $files['index.html'] = $htmlFile;
        } elseif ($htmlContent) {
            $files['index.html'] = $htmlContent;
        }
        return $this->sendGotenbergRequest('/forms/chromium/convert/html', $files);
    }

    public function generatePdfFromOffice(UploadedFile $officeFile): ?string
    {
        return $this->sendGotenbergRequest('/forms/libreoffice/convert', ['files' => $officeFile]);
    }

    public function generatePdfFromMarkdown(UploadedFile $markdownFile): ?string
    {
        return $this->sendGotenbergRequest('/forms/libreoffice/convert', ['files' => $markdownFile]);
    }

    public function generatePdfFromImage(UploadedFile $imageFile): ?string
    {
        return $this->sendGotenbergRequest('/forms/libreoffice/convert', ['files' => $imageFile]);
    }

    public function mergePdfs(array $files): ?string
    {
        $dataParts = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $dataParts[] = DataPart::fromPath($file->getRealPath());
            }
        }
        return $this->sendGotenbergRequest('/forms/pdfengines/merge', ['files' => $dataParts]);
    }

    public function generateScreenshotFromUrl(string $url): ?string
    {
        // Note: This returns a PNG, not a PDF. The response headers should be adjusted in the controller.
        return $this->sendGotenbergRequest('/forms/chromium/screenshot/url', [], ['url' => $url]);
    }
}
