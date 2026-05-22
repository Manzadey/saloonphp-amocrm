<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12'                  => true,
        'declare_strict_types'    => true,
        'no_unused_imports'       => true,
        'ordered_imports'         => ['sort_algorithm' => 'alpha'],
        'single_blank_line_at_eof' => true,
        'no_extra_blank_lines'    => true,
        'no_trailing_whitespace'  => true,
    ])
    ->setFinder($finder);
