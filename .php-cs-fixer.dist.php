<?php

use PhpCsFixer\Config;

$finder = PhpCsFixer\Finder::create()
	->in(__DIR__);

return (new Config())
	->setRiskyAllowed(true)
	->setRules([
		'@PSR12' => true,
		'declare_strict_types' => true,
		'braces' => ['position_after_functions_and_oop_constructs' => 'same']
	])
	->setIndent("\t")
	->setLineEnding("\n")
	->setFinder($finder);
