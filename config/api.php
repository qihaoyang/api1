<?php
return [
    'rate_limits' => [
        'access' => env('RATE_LIMITS', '60,1'),
        'sign'   => env('SING_RATE_LIMITS', '10,1'),
    ]
];
