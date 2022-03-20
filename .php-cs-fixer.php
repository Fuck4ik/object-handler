<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('examples')
    ->exclude('tools')
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
//        '@Symfony:risky' => true,
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer' => true,
        'no_superfluous_phpdoc_tags' => true,
        'concat_space' => ['spacing' => 'one'],
        'cast_spaces' => ['space' => 'none'],
        'array_syntax' => ['syntax' => 'short'],
        'protected_to_private' => false,
        'native_function_invocation' => false,
        'native_constant_invocation' => false,
        'phpdoc_summary' => false,
        'phpdoc_to_comment' => false,
        'function_declaration' => ['closure_function_spacing' => 'none'],
    ])
    ->setFinder($finder)
;
