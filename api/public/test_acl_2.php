<?php

$pdo = new PDO('mysql:host=127.0.0.1;dbname=symfony_acl;charset=utf8mb4', 'root', '');

// Fetch group permissions
$stmt = $pdo->prepare("SELECT g.name as group_name, p.name as permission_name, p.codename FROM users u JOIN user_groups ug ON u.id = ug.user_id JOIN groups g ON ug.group_id = g.id LEFT JOIN group_permissions gp ON g.id = gp.group_id LEFT JOIN permissions p ON gp.permission_id = p.id WHERE u.email = 'john@yopmail.com'");
$stmt->execute();
$groupPerms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch direct permissions
$stmt2 = $pdo->prepare("SELECT p.name as permission_name, p.codename FROM users u JOIN user_permissions up ON u.id = up.user_id JOIN permissions p ON up.permission_id = p.id WHERE u.email = 'john@yopmail.com'");
$stmt2->execute();
$directPerms = $stmt2->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['group_permissions' => $groupPerms, 'direct_permissions' => $directPerms]);
