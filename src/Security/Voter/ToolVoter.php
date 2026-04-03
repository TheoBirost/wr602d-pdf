<?php

namespace App\Security\Voter;

use App\Entity\Tool;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ToolVoter extends Voter
{
    public const VIEW = 'TOOL_VIEW';

    // Hiérarchie des plans, du moins cher/restrictif au plus cher/permissif
    private const PLAN_HIERARCHY = ['STARTER', 'PRO', 'ELITE', 'LEGEND'];

    protected function supports(string $attribute, $subject): bool
    {
        // Le voter ne s'applique que pour l'attribut VIEW et un objet Tool
        return $attribute === self::VIEW && $subject instanceof Tool;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Si l'utilisateur n'est pas connecté, on refuse l'accès
        if (!$user instanceof User) {
            return false;
        }

        /** @var Tool $tool */
        $tool = $subject;
        $userPlan = $user->getPlan();

        // Si l'utilisateur n'a pas de plan, on refuse
        if (!$userPlan) {
            return false;
        }

        $toolRequiredPlans = $tool->getPlans();

        // Si l'outil ne requiert aucun plan, personne n'y a accès par sécurité
        if (empty($toolRequiredPlans)) {
            return false;
        }

        // Normalise le nom du plan de l'utilisateur (ex: "starter" -> "STARTER")
        $currentUserPlanName = strtoupper($userPlan->getName());
        
        // Trouve la position (l'index) du plan de l'utilisateur dans la hiérarchie
        $currentUserPlanIndex = array_search($currentUserPlanName, self::PLAN_HIERARCHY);

        // Si le plan de l'utilisateur n'est pas dans notre hiérarchie, on refuse
        if ($currentUserPlanIndex === false) {
            return false;
        }

        // L'outil est accessible si le niveau du plan de l'utilisateur est suffisant
        // pour AU MOINS UN des plans requis par l'outil.
        // En général, un outil n'a qu'un seul plan minimum requis.
        foreach ($toolRequiredPlans as $requiredPlanName) {
            // Normalise le nom du plan requis
            $requiredPlanIndex = array_search(strtoupper($requiredPlanName), self::PLAN_HIERARCHY);
            
            // Si le plan requis existe et que le niveau de l'utilisateur est supérieur ou égal,
            // alors l'accès est accordé.
            if ($requiredPlanIndex !== false && $currentUserPlanIndex >= $requiredPlanIndex) {
                return true;
            }
        }

        // Si aucune des conditions n'est remplie après avoir vérifié tous les plans requis, on refuse.
        return false;
    }
}
