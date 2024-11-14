<?php

require __DIR__ . '/vendor/autoload.php';

$finder = PhpCsFixer\Finder::create()->in([
    __DIR__ . DIRECTORY_SEPARATOR . 'src',
    __DIR__ . DIRECTORY_SEPARATOR . 'tests',
])->notPath(['cache', 'vendor']);

return PHLAK\CodingStandards\ConfigFactory::make($finder);
