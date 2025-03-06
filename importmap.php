<?php

return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'controllers' => [
        'path' => './assets/controllers.js',
        'entrypoint' => true,
    ],
    'bootstrap' => [
        'path' => './assets/bootstrap.js',
    ],
    'styles/app' => [
        'path' => './assets/styles/app.css',
        'type' => 'css',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => '@symfony/stimulus-bundle/loader.js',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.3',
        'type' => 'css',
    ],
];
