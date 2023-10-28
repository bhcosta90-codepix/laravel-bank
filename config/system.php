<?php

declare(strict_types=1);

return [
    'bank' => env('BANK_ID', '4c0ecd48-8000-4eae-9eb0-640b2144335c'),
    'endpoint' => [
        'central' => env('DOMAIN_CENTRAL', 'http://host.docker.internal:7000')
    ],
];
