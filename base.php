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
$container->createProxyGenerator();

/*
$manager = new ORM\Manager($container->database);
$repository = new Repositories\ArticleRepository(
	new ORM\Mappers\NetteDatabaseMapper($container->database, $manager, 'Entities\Article')
);
*/

$tag1 = new Entities\Tag;
$tag1->setName('Nazov tagu ' . Strings::random(4));
$tag2 = new Entities\Tag;
$tag2->setName('Nazov tagu ' . Strings::random(4));


$article = $container->article->find(649);
//$article = new Entities\Article;
$article->setTitle('Moja titulka ' . Strings::random(4));
$article->setContent('Obrash stránky');
$article->setCreated(new Nette\DateTime);
$article->setCategory($container->category->find(673));
//$article->addTag($tag1);
//$article->addTag($container->tag->find(222));
//$article->addTag($tag2);

debug($article);
//foreach ($article->getTags() as $tag) {
	//debug($tag);
//}

$container->article->save($article);


exit;
$collection = new ORM\Collections\ArrayCollection($article, 'Entities\Tag');

debug($collection);

$mapper = $container->tag->getMapper();
debug($mapper->toMany($collection, $article));



//$mapper->getManyToMany();

//$container->article->delete($article);