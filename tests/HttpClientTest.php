<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClientTest extends TestCase
{
    private HttpClientInterface $client;

    protected function setUp(): void
    {

        $this->client = new MockHttpClient();
    }

    /**
     * Test simple - Requête GET vers GitHub
     */
    public function testFetchGitHubInformation(): void
    {

        $mockData = [
            'id' => 521583,
            'name' => 'symfony-docs',
            'full_name' => 'symfony/symfony-docs',
            'description' => 'The Symfony documentation',
            'stargazers_count' => 2100
        ];


        $mockResponse = new MockResponse(json_encode($mockData), [
            'http_code' => 200,
            'response_headers' => ['content-type' => 'application/json']
        ]);

        $this->client = new MockHttpClient($mockResponse);


        $response = $this->client->request(
            'GET',
            'https://api.github.com/repos/symfony/symfony-docs'
        );


        $statusCode = $response->getStatusCode();
        $this->assertEquals(200, $statusCode);


        $contentType = $response->getHeaders()['content-type'][0];
        $this->assertEquals('application/json', $contentType);


        $content = $response->toArray();
        $this->assertEquals(521583, $content['id']);
        $this->assertEquals('symfony-docs', $content['name']);
    }

    /**
     * Test simple - Requête POST
     */
    public function testPostRequest(): void
    {

        $mockResponse = new MockResponse(json_encode([
            'success' => true,
            'message' => 'Données créées'
        ]), [
            'http_code' => 201,
            'response_headers' => ['content-type' => 'application/json']
        ]);

        $this->client = new MockHttpClient($mockResponse);


        $response = $this->client->request('POST', 'https://example.com/api/post', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode(['key' => 'value']),
        ]);


        $statusCode = $response->getStatusCode();
        $this->assertEquals(201, $statusCode);

        $content = $response->toArray();
        $this->assertTrue($content['success']);
        $this->assertEquals('Données créées', $content['message']);
    }

    /**
     * Test simple - Vérifier que les données sont bien au format array
     */
    public function testResponseIsArray(): void
    {
        $mockData = ['name' => 'test', 'value' => 123];

        $mockResponse = new MockResponse(json_encode($mockData));
        $this->client = new MockHttpClient($mockResponse);

        $response = $this->client->request('GET', 'https://api.example.com/test');
        $content = $response->toArray();

        $this->assertIsArray($content);
        $this->assertArrayHasKey('name', $content);
        $this->assertArrayHasKey('value', $content);
    }

    /**
     * Test simple - Code 200 OK
     */
    public function testStatusCode200(): void
    {
        $mockResponse = new MockResponse('{"status":"ok"}', [
            'http_code' => 200
        ]);

        $this->client = new MockHttpClient($mockResponse);
        $response = $this->client->request('GET', 'https://api.example.com');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test simple - Code 404 Not Found
     */
    public function testStatusCode404(): void
    {
        $mockResponse = new MockResponse('', [
            'http_code' => 404
        ]);

        $this->client = new MockHttpClient($mockResponse);
        $response = $this->client->request('GET', 'https://api.example.com/not-found');

        $this->assertEquals(404, $response->getStatusCode());
    }
}