<?php
$context = stream_context_create([
    'http' => [
        'ignore_errors' => true
    ]
]);
$html = file_get_contents('http://localhost:3000/login', false, $context);
echo "RESPONSE CODE: " . $http_response_header[0] . "\n\n";
// Extract error message from Nuxt 500 page
// Usually Nuxt error pages have <div class="error-message"> or similar, but let's just strip tags except title/h1
echo strip_tags($html);
