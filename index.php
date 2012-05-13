<?php

use Nette\Diagnostics\Debugger,
	Nette\Utils\Html,
	Nette\Utils\Strings;

// Load Nette Framework
require __DIR__ . '/libs/Nette/loader.php';
require __DIR__ . '/libs/tools.php';

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

/*
$manager = new ORM\Manager($container->database);
$repository = new Repositories\ArticleRepository(
	new ORM\Mappers\NetteDatabaseMapper($container->database, $manager, 'Entities\Article')
);
*/



$articleService = new Services\Admin\Article($container->article, $container->article->find(536));
//$articleService->setTitle('Moja titulka ' . Strings::random(4));
$articleService->setContent('Obrash stránky');
//$articleService->save();
//$articleService->publish();

debug($articleService);