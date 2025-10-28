<?php

use Isolated\Symfony\Component\Finder\Finder;

return [
    'prefix' => 'Tassos\\Vendor',
    'output-dir' => 'vendor',
    'finders' => [
        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->notName('/LICENSE|.*\\.md|.*\\.dist|Makefile|composer\\.json|composer\\.lock/')
            ->exclude([
                'doc',
                'test',
                'test_old',
                'tests',
                'Tests',
                'vendor-bin',
            ])
            ->in('vendor')
    ],
];