<?php

return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
        'type' => 'js',
    ],
    'gsap' => [
        'version' => '3.12.5',
    ],
    'animation' => [
        'path' => './assets/animation.js',
        'entrypoint' => true,
        'type' => 'js',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
        'type' => 'js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
];