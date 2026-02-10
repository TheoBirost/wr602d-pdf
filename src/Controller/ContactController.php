<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ContactController extends AbstractController
{
    #[Route('/contacts', name: 'app_contacts')]
    public function index(): Response
    {
        // Logique pour afficher la liste des contacts
        return $this->render('contact/index.html.twig');
    }
}
