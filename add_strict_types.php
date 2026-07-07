<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/api/src');
$iterator = new RecursiveIteratorIterator($dir);

$count = 0;
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        
        // Check if declare(strict_types=1) is already there
        if (strpos($content, 'declare(strict_types=1)') === false) {
            // Replace <?php with <?php + declare(strict_types=1);
            $newContent = preg_replace('/^<\?php\s*/', "<?php\n\ndeclare(strict_types=1);\n\n", $content);
            if ($newContent !== null && $newContent !== $content) {
                file_put_contents($file->getPathname(), $newContent);
                echo "Updated: " . $file->getFilename() . "\n";
                $count++;
            }
        }
    }
}

echo "\nDone! Added declare(strict_types=1) to $count files.\n";
