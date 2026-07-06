<?php
$data = json_encode(['email' => 'admin@yopmail.com', 'password' => 'Test@1234']);
$ch = curl_init('http://127.0.0.1:8000/api/v1/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$result = curl_exec($ch);
curl_close($ch);
echo json_encode(json_decode($result), JSON_PRETTY_PRINT);
