<?php

$context = stream_context_create([
    'http' => [
        'ignore_errors' => true,
    ],
]);
$html = file_get_contents('http://localhost:3000/login', false, $context);
preg_match('/id="__NUXT_DATA__"[^>]*>([^<]+)<\/script>/', $html, $matches);
if (isset($matches[1])) {
    echo "NUXT DATA FOUND\n";
    $json = json_decode($matches[1], true);
    if ($json) {
        // Output raw data for inspection
        print_r($json);
    } else {
        echo 'Could not decode JSON: '.$matches[1];
    }
} else {
    echo "NO NUXT DATA\n";
    echo strip_tags($html);
}
