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
                    // For Gotenberg, the field name is usually 'files'
                    // But for HTML conversion, one file MUST be named 'index.html'
                    // We handle this by checking if the key is 'index.html' or similar if needed,
                    // but standard multipart upload uses the filename property.
                    
                    // If the key in $files array is 'index.html', we force the filename to be index.html
                    $filename = $file->getClientOriginalName();
                    if ($name === 'index.html') {
                        $filename = 'index.html';
                        // The field name for Gotenberg is still 'files' usually
                        $name = 'files'; 
                    }
                    
                    $formFieldsData[$name] = DataPart::fromPath($file->getRealPath(), $filename);
                } elseif (is_string($file)) { 
                    // String content (e.g. raw HTML)
                    // If the key is 'index.html', we set the filename to index.html
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
            // We use 'index.html' as the key to signal our sendGotenbergRequest to name the file 'index.html'
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
}
