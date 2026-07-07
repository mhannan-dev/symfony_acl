<?php

declare(strict_types=1);

$filesToDelete = [
    __DIR__ . '/frontend/app/pages/permissions/new.vue',
    __DIR__ . '/frontend/app/pages/permissions/[id].vue',
    __DIR__ . '/api/src/Controller/Admin/ActivityLogController.php',
];

$count = 0;
foreach ($filesToDelete as $path) {
    if (file_exists($path)) {
        if (unlink($path)) {
            echo "Deleted: $path\n";
            $count++;
        } else {
            echo "Failed to delete: $path\n";
        }
    }
}

// Clean up Admin directory if empty
$adminDir = __DIR__ . '/api/src/Controller/Admin';
if (is_dir($adminDir)) {
    $files = array_diff(scandir($adminDir), ['.', '..']);
    if (empty($files)) {
        rmdir($adminDir);
        echo "Deleted empty directory: $adminDir\n";
    }
}

echo "\nDone! Deleted $count unused files.\n";
