<?php

$context = stream_context_create([
    'http' => [
        'ignore_errors' => true,
    ],
]);
$html = file_get_contents('http://localhost:3000/login', false, $context);
echo 'RESPONSE CODE: '.$http_response_header[0]."\n\n";

// Output raw HTML but base64 encode it so my markdown parser doesn't strip it!
echo base64_encode($html);
