<?php

$url = 'http://127.0.0.1:8000/api/v1/login';
$data = ['email' => 'admin@yopmail.com', 'password' => 'Test@1234'];
$options = [
    'http' => [
        'header' => "Content-type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data),
    ],
];
$context = stream_context_create($options);
$result = @file_get_contents($url, false, $context);
if (false === $result) {
    echo "HTTP Request failed. Response Headers:\n";
    print_r($http_response_header);
}
echo "\nResponse Body:\n";
echo $result;
