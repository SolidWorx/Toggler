<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src');

return PhpCsFixer\Config::create()
    ->setRules(
        [
            '@PSR1' => true,
            '@PSR2' => true,
            '@Symfony' => true,
            'array_syntax' => array('syntax' => 'short'),
            'phpdoc_no_package' => true,
            'phpdoc_summary' => false,
            'declare_strict_types' => true,
            'strict_param' => true,
            'global_namespace_import' => [
                'import_classes' => true,
                'import_constants' => true,
                'import_functions' => true,
            ],
            'ordered_imports' => true,
        ]
    )
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
