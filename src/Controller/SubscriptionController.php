<?php

namespace App\Controller;

use App\Repository\PlanRepository;
use App\Repository\ToolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/subscription')]
class SubscriptionController extends AbstractController
{
    #[Route('/change', name: 'subscription_change', methods: ['GET'])]
    public function changeSubscription(PlanRepository $planRepository, ToolRepository $toolRepository): Response
    {
        $plans = $planRepository->findBy(['active' => true], ['price' => 'ASC']);
        $tools = $toolRepository->findAll();
        
        return $this->render('subscription/change.html.twig', [
            'plans' => $plans,
            'tools' => $tools,
            'current_plan' => $this->getUser()->getPlan(),
        ]);
    }

    #[Route('/change/{id}', name: 'subscription_change_handle', methods: ['POST'])]
    public function handleChangeSubscription(int $id, PlanRepository $planRepository, EntityManagerInterface $em, Request $request): Response
    {
        // CSRF token validation
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('change-plan', $submittedToken)) {
            $this->addFlash('error', 'Invalid request. Please try again.');
            return $this->redirectToRoute('subscription_change');
        }

        $newPlan = $planRepository->find($id);
        if (!$newPlan || !$newPlan->isActive()) {
            $this->addFlash('error', 'The selected plan is not valid.');
            return $this->redirectToRoute('subscription_change');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // Update user's plan and reset generation count
        $user->setPlan($newPlan);
        $user->setGenerationsUsed(0);
        
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Your plan has been successfully updated to ' . $newPlan->getName() . '!');

        return $this->redirectToRoute('homepage');
    }
}
