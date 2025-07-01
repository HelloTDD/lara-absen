<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/resources',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12'                          => true,
        'array_syntax'                    => ['syntax' => 'short'],
        'binary_operator_spaces'          => ['default' => 'align_single_space'],
        'blank_line_after_namespace'      => true,
        'blank_line_after_opening_tag'    => true,
        'blank_line_before_statement'     => ['statements' => ['return']],
        'class_attributes_separation'     => ['elements' => ['method' => 'one']],
        'combine_consecutive_unsets'      => true,
        'declare_equal_normalize'         => ['space' => 'single'],
        'method_argument_space'           => ['on_multiline' => 'ensure_fully_multiline'],
        'no_extra_blank_lines'            => ['tokens' => ['extra', 'throw', 'use']],
        'no_unused_imports'               => true,
        'ordered_imports'                 => ['sort_algorithm' => 'alpha'],
        'single_quote'                    => true,
        'ternary_operator_spaces'         => true,
        'trailing_comma_in_multiline'     => ['elements' => ['arrays']],
        'whitespace_after_comma_in_array' => true,
    ])
    ->setFinder($finder);
