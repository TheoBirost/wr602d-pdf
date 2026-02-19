<?php

namespace App\Controller;

use App\Repository\PlanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    #[Route('/subscription/change', name: 'subscription_change')]
    public function changeSubscription(PlanRepository $planRepository): Response
    {
        $plans = $planRepository->findBy(['active' => true], ['price' => 'ASC']);

        return $this->render('subscription/change.html.twig', [
            'plans' => $plans,
        ]);
    }
}
