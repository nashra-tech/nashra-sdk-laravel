<?php

return [
    'api_key' => env('NASHRA_API_KEY'),
    'base_url' => env('NASHRA_BASE_URL', 'https://app.nashra.ai/api/v1'),
    'timeout' => env('NASHRA_TIMEOUT', 30),
    'max_retries' => env('NASHRA_MAX_RETRIES', 3),
];
