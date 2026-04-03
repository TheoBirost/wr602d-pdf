<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        // Vous pouvez adapter ce test pour qu'il corresponde au contenu de votre page d'accueil
        // Par exemple, en vérifiant la présence d'un titre spécifique.
        // $this->assertSelectorTextContains('h1', 'Bienvenue');
    }
}
