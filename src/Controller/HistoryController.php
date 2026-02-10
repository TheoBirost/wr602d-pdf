<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class HistoryController extends AbstractController
{
    #[Route('/history', name: 'app_history')]
    public function index(): Response
    {
        // Logique pour afficher l'historique des générations
        return $this->render('history/index.html.twig');
    }
}
