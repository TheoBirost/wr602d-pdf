<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PdfControllerTest extends WebTestCase
{
    public function testGeneratePdfRequiresLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pdf/generate');

        $this->assertResponseRedirects('/login');
    }

    public function testGeneratePdfAsUser(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@example.com');
        $client->loginUser($testUser);

        $client->request('GET', '/pdf/generate');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/pdf');
    }
}
