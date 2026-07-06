<?php

$dir = new RecursiveDirectoryIterator('E:\laragon\www\symfony_acl\api', RecursiveDirectoryIterator::SKIP_DOTS);
$iterator = new RecursiveIteratorIterator($dir);

$count = 0;
foreach ($iterator as $file) {
    if ($file->getExtension() === 'php' && $file->getFilename() !== 'add_strict_types.php') {
        $path = $file->getPathname();
        
        // Skip vendor and var directories
        if (strpos($path, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false || 
            strpos($path, DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR) !== false) {
            continue;
        }

        $content = file_get_contents($path);
        
        // Ensure it starts with <?php
        if (strpos(trim($content), '<?php') !== 0) {
            continue;
        }

        // Check if it already has declare(strict_types
        if (preg_match('/declare\s*\(\s*strict_types\s*=\s*1\s*\)/i', $content)) {
            continue;
        }
        
        // Replace the first <?php with <?php\n\ndeclare(strict_types=1);\n\n
        $content = preg_replace('/^<\?php\s*/', "<?php\n\ndeclare(strict_types=1);\n\n", ltrim($content));
        
        if (file_put_contents($path, $content)) {
            echo "Updated: $path\n";
            $count++;
        }
    }
}
echo "Total updated: $count\nDone.\n";
