<?php

use Nette\Diagnostics\Debugger,
	Nette\Utils\Html,
	Nette\Utils\Strings;

// Load Nette Framework
require './libs/Nette/loader.php';
require './libs/tools.php';

Debugger::enable();

// Load configuration from config.neon
$configurator = new Nette\Config\Configurator;
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->addConfig(__DIR__ . '/config.neon');
$configurator->createRobotLoader()
	->addDirectory(__DIR__ . '/app')
	->addDirectory(__DIR__ . '/libs')
	->register();
$container = $configurator->createContainer();
$container->createProxyGenerator();


