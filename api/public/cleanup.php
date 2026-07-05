<?php
$filesToDelete = [
    __DIR__ . '/../src/Controller/Api/ActivityLogController.php',
    __DIR__ . '/../src/Controller/Api/ContentTypeController.php',
    __DIR__ . '/../src/Controller/Api/GroupController.php',
    __DIR__ . '/../src/Controller/Api/PermissionController.php',
    __DIR__ . '/../src/Controller/Api/UserController.php',
];

$results = [];

foreach ($filesToDelete as $file) {
    if (file_exists($file)) {
        $deleted = unlink($file);
        $results[] = "Deleted file $file: " . ($deleted ? 'Success' : 'Failed');
    }
}

// Clear Cache
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}

$cacheCleared = deleteDirectory(__DIR__ . '/../var/cache/dev');
$results[] = "Cleared cache: " . ($cacheCleared ? 'Success' : 'Failed');

echo implode("\n", $results);
echo "\nCleanup finished.";
