<?php

$pdo = new PDO('mysql:host=127.0.0.1;dbname=symfony_acl;charset=utf8mb4', 'root', '');
$stmt1 = $pdo->prepare("SELECT p.name, p.codename FROM users u JOIN user_permissions up ON u.id = up.user_id JOIN permissions p ON up.permission_id = p.id WHERE u.email = 'john@yopmail.com'");
$stmt1->execute();
$direct = $stmt1->fetchAll(PDO::FETCH_ASSOC);
echo 'Direct User Permissions: '.json_encode($direct)."\n";
