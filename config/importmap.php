<?php

use Symfony\Component\AssetMapper\ImportMap\ImportMapConfigReader;

return function (ImportMapConfigReader $reader) {
    return [
        'app' => [
            'path' => 'assets/app.js',
            'entrypoint' => true,
        ],
        'animation' => [
            'path' => 'assets/animation.js',
            'entrypoint' => true,
        ],
        'gsap' => [
            'path' => 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
            'type' => 'js',
        ],
    ];
};
