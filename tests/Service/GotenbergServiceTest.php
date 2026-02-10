<?php

namespace App\Tests\Service;

use App\Service\GotenbergService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GotenbergServiceTest extends TestCase
{
    public function testGeneratePdfFromHtmlSuccessfully(): void
    {
        $mockPdfContent = '%PDF-1.4...'; // Simplified mock PDF content

        // Mock the ResponseInterface
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('getContent')->willReturn($mockPdfContent);

        // Mock the HttpClientInterface
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')->willReturn($mockResponse);

        $gotenbergService = new GotenbergService($mockHttpClient, 'http://localhost:3000');

        $htmlContent = '<h1>Test HTML</h1><p>Some content</p>';
        $pdf = $gotenbergService->generatePdfFromHtml($htmlContent);

        $this->assertIsString($pdf);
        $this->assertStringContainsString($mockPdfContent, $pdf);
    }

    public function testGeneratePdfFromHtmlFailure(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to generate PDF: Gotenberg Error');

        // Mock the ResponseInterface for a failed response
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(500);
        $mockResponse->method('getContent')->willReturn('Gotenberg Error');

        // Mock the HttpClientInterface for a failed response
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')->willReturn($mockResponse);

        $gotenbergService = new GotenbergService($mockHttpClient, 'http://localhost:3000');

        $htmlContent = '<h1>Test HTML</h1><p>Some content</p>';
        $gotenbergService->generatePdfFromHtml($htmlContent);
    }
}
