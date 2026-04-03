<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    // Forcer l'environnement de production et désactiver le mode debug
    // C'est une sécurité pour s'assurer que l'application ne tourne jamais en mode dev en production.
    $env = $context['APP_ENV'] ?? 'prod';
    $debug = (bool) ($context['APP_DEBUG'] ?? false);

    // Si l'environnement n'est pas 'prod', on le force à 'prod' et on désactive le debug.
    if ($env !== 'prod') {
        $env = 'prod';
        $debug = false;
    }

    return new Kernel($env, $debug);
};
