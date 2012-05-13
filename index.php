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


$date = new Nette\DateTime;
$name = 'Kategória';
$title = 'Moja titulka ' . Strings::random(4);
$content = 'Obrash stránky';

$cat = new Entities\Category;
$cat->setName($name);
$cat->setCreated($date);
$container->category->save($cat);

/*
$cat1 = new Entities\Category;
$cat1->setName('Kategória ' . Strings::random(4));
$cat1->setCreated($date);
$cat1->setCategory($cat);
$container->category->save($cat1);

$cat2 = new Entities\Category;
$cat2->setName('Kategória ' . Strings::random(4));
$cat2->setCreated($date);
$cat2->setCategory($cat);
$container->category->save($cat2);

$cat3 = new Entities\Category;
$cat3->setName('Kategória ' . Strings::random(4));
$cat3->setCreated($date);
$cat3->setCategory($cat);
$container->category->save($cat3);
*/

$cat4 = new Entities\Category;
$cat4->setName('Kategória ' . Strings::random(4));
$cat4->setCreated($date);
$cat4->setCategory($cat);
$container->category->save($cat4);

$cat4->setCategory(null);

$container->category->save($cat4);

debug($cat4->getId(), count($cat->getChildren()));

//foreach ($cat->getChildren() as $entity) {
	//debug($entity);
//}

exit;

//debug($repository->find(1));
//debug($repository->find(1));
//debug($repository->findAll());
//debug($repository->findBy(array('webalized' => 'moja-prva-titulka')));
/*

foreach ($repository->find(1)->getTags() as $entity) {
	debug($entity);
}
*/
//$article = $repository->find(1);

$tag1 = new Entities\Tag;
$tag1->setName('tag1');
$tag1->setCreated(new Nette\DateTime);
$tag2 = new Entities\Tag;
$tag2->setName('tag2');
$tag2->setCreated(new Nette\DateTime);

//$container->tag->save($tag2);

$cat = new Entities\Category;
$cat->setName('cat1');
$cat->setCreated(new Nette\DateTime);


$article = new Entities\Article;
$article->setTitle('Titulllllkkkkaaaa');
$article->setContent('Konreeeeeent');
$article->setCreated(new Nette\DateTime);
//$article->addTag($tag1)->addTag($tag2);
$article->setCategory($cat);

$container->article->save($article);

$article = $container->article->find($article->getId());
//$article = $container->article->find(78);
//debug(count($article->getTags()));
//debug($article->getTags()->count());
//debug($article);
//foreach ($article->getTags() as $entity) {
	//debug($entity);
//}

//$article->setCategory($cat);
debug($article->getCategory());

//debug($article->getTags()->count());
//debug($article);
//$container->article->save($article);
