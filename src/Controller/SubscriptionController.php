<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    #[Route('/subscription/change', name: 'subscription_change')]
    public function changeSubscription(): Response
    {
        // Logic for changing subscription
        return $this->render('subscription/change.html.twig');
    }
}
