<?php

namespace App\Controller;

use App\Entity\Plan;
use App\Repository\PlanRepository;
use App\Repository\ToolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/subscription')]
class SubscriptionController extends AbstractController
{
    #[Route('/', name: 'app_subscription', methods: ['GET'])]
    public function index(PlanRepository $planRepository, ToolRepository $toolRepository): Response
    {
        $plans = $planRepository->findBy(['active' => true], ['price' => 'ASC']);
        $tools = $toolRepository->findAll();

        return $this->render('subscription/change.html.twig', [
            'plans' => $plans,
            'tools' => $tools,
        ]);
    }
}
