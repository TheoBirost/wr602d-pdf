<?php

namespace App\Twig;

use App\Entity\Plan;
use App\Entity\Tool;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_tool_in_plan', [$this, 'isToolInPlan']),
        ];
    }

    public function isToolInPlan(Tool $tool, Plan $plan, array $plans): bool
    {
        $planNames = array_map(fn($p) => $p->getName(), $plans);
        $planIndex = array_search($plan->getName(), $planNames);

        foreach ($tool->getPlans() as $toolPlan) {
            $toolPlanIndex = array_search($toolPlan, $planNames);
            if ($toolPlanIndex !== false && $planIndex >= $toolPlanIndex) {
                return true;
            }
        }

        return false;
    }
}
