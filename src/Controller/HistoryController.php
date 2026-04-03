<?php

namespace App\Controller;

use App\Repository\GenerationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/history')]
class HistoryController extends AbstractController
{
    #[Route('/', name: 'history_index', methods: ['GET'])]
    public function index(GenerationRepository $generationRepository, Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $filter = $request->query->get('filter');

        $criteria = ['user' => $user];
        if ($filter === 'favorites') {
            $criteria['isFavorite'] = true;
        } elseif ($filter) {
            $criteria['toolName'] = $filter;
        }

        $generations = $generationRepository->findBy(
            $criteria,
            ['generatedAt' => 'DESC']
        );

        // Calcul des statistiques
        $allGenerations = $generationRepository->findBy(['user' => $user]);
        $stats = [
            'all_time' => count($allGenerations),
            'this_month' => 0,
            'last_30_days' => 0,
            'favorites' => 0,
        ];

        $now = new \DateTimeImmutable();
        $monthStart = new \DateTimeImmutable('first day of this month midnight');
        $thirtyDaysAgo = $now->sub(new \DateInterval('P30D'));

        foreach ($allGenerations as $generation) {
            if ($generation->getGeneratedAt() >= $monthStart) {
                $stats['this_month']++;
            }
            if ($generation->getGeneratedAt() >= $thirtyDaysAgo) {
                $stats['last_30_days']++;
            }
            if ($generation->isFavorite()) {
                $stats['favorites']++;
            }
        }
        
        $categories = $generationRepository->createQueryBuilder('g')
            ->select('g.toolName')
            ->where('g.user = :user')
            ->setParameter('user', $user)
            ->distinct()
            ->getQuery()
            ->getResult();

        return $this->render('history/index.html.twig', [
            'generations' => $generations,
            'stats' => $stats,
            'categories' => array_column($categories, 'toolName'),
            'activeFilter' => $filter,
        ]);
    }

    #[Route('/toggle-favorite/{id}', name: 'history_toggle_favorite', methods: ['POST'])]
    public function toggleFavorite(int $id, GenerationRepository $generationRepository, EntityManagerInterface $em, Request $request): Response
    {
        // Validation du token CSRF pour la sécurité
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('toggle-favorite'.$id, $submittedToken)) {
            $this->addFlash('error', 'Invalid request.');
            return $this->redirectToRoute('history_index');
        }

        $generation = $generationRepository->find($id);

        // Vérifier que la génération existe et qu'elle appartient bien à l'utilisateur connecté
        if (!$generation || $generation->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Generation not found.');
            return $this->redirectToRoute('history_index');
        }

        // Inverser le statut de favori
        $generation->setFavorite(!$generation->isFavorite());
        $em->flush();

        return $this->redirectToRoute('history_index');
    }
}
