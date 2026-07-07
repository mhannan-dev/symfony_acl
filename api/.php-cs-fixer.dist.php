<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->notPath([
        'config/bundles.php',
        'config/reference.php',
    ])
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'declare_strict_types' => false,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const']],
        'phpdoc_align' => false,
        'phpdoc_separation' => false,
        'single_line_throw' => false,
    ])
    ->setFinder($finder)
;
