<?php

namespace App\Twig;

use App\Entity\Plan;
use App\Entity\Tool;
use App\Entity\User;
use App\Security\Voter\ToolVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private const PLAN_HIERARCHY = ['STARTER', 'PRO', 'ELITE', 'LEGEND'];
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_tool_in_plan', [$this, 'isToolInPlan']),
            new TwigFunction('can_user_use_tool', [$this, 'canUserUseTool']),
        ];
    }

    public function isToolInPlan(Tool $tool, Plan $plan): bool
    {
        $toolPlans = $tool->getPlans();

        if (empty($toolPlans)) {
            return false;
        }

        $currentPlanName = strtoupper($plan->getName());
        $currentPlanIndex = array_search($currentPlanName, self::PLAN_HIERARCHY);

        if ($currentPlanIndex === false) {
            return false;
        }

        foreach ($toolPlans as $requiredPlanName) {
            $requiredPlanIndex = array_search(strtoupper($requiredPlanName), self::PLAN_HIERARCHY);
            if ($requiredPlanIndex !== false && $currentPlanIndex >= $requiredPlanIndex) {
                return true;
            }
        }

        return false;
    }

    public function canUserUseTool(User $user, Tool $tool): bool
    {
        return $this->authorizationChecker->isGranted(ToolVoter::VIEW, $tool);
    }
}
