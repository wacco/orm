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
$configurator->setTempDirectory('./temp');
$configurator->addConfig('./config.neon');
$configurator->createRobotLoader()
	->addDirectory('./app')
	->addDirectory('./libs')
	->register();
$container = $configurator->createContainer();

$manager = new ORM\Manager($container->database);
$repository = new Repositories\ArticleRepository(
	new ORM\Mappers\NetteDatabaseMapper($container->database, $manager, 'Entities\Article')
);

$article = new Entities\Article;
$article->setTitle('Moja titulka ' . Strings::random(4));
$article->setContent('Obrash stránky');
$article->setCreated(new Nette\DateTime);

$repository->save($article);

debug($article->getId());

$repository->delete($article);