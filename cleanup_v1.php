<?php

declare(strict_types=1);

$dir = __DIR__ . '/api/src/Controller/Api/';

$filesToDelete = [
    'UserController.php',
    'PermissionController.php',
    'GroupController.php',
    'ContentTypeController.php',
    'ActivityLogController.php',
    'DashboardController.php',
];

$count = 0;
foreach ($filesToDelete as $filename) {
    $path = $dir . $filename;
    if (file_exists($path)) {
        if (unlink($path)) {
            echo "Deleted: $filename\n";
            $count++;
        } else {
            echo "Failed to delete: $filename\n";
        }
    }
}

echo "\nDone! Deleted $count old API controller files to resolve V1 conflicts.\n";
