<?php

use Nette\Utils\Strings;

require __DIR__ . '/../bootstrap.php';

$manager = new ORM\Manager($container->database);
$articleRepository = $manager->getRepository('Entities\Article');
$categoryRepository = $manager->getRepository('Entities\Category');

$date = new Nette\DateTime;
$name = 'Kategória';
$title = 'Moja titulka ' . Strings::random(4);
$content = 'Obrash stránky';

$cat = new Entities\Category;
$cat->setName($name);
$cat->setCreated($date);

$article = new Entities\Article;
$article->setTitle($title);
$article->setContent($content);
$article->setCreated($date);
$article->setCategory($cat);


// overim zhodnu kategoriu / objekt
Assert::same($cat, $article->getCategory());

$articleRepository->save($article);

// zistim ci kategoria dostala ID
Assert::match('%d%', $cat->getId());

// zistim ci clanok dostal ID
Assert::match('%d%', $article->getId());

// overim zhodnu kategoriu / objekt
Assert::same($cat, $article->getCategory());

$entity = $articleRepository->find($article->getId());

// zistim ci mi find vrati vytvorenu entitu
Assert::instance('Entities\Category', $entity->getCategory());

// overim nasetovane a ulozene data
Assert::same($name, $entity->getCategory()->getName());
Assert::equal($date, $entity->getCategory()->getCreated());

// vymazem clanok
Assert::true($articleRepository->delete($article));

// overim chybu pri mazani kategorie cez repozitar clanku
Assert::throws(function() use($articleRepository, $cat) {
	$articleRepository->delete($cat);
}, 'Exception');

// vymazem kategoriu
Assert::true($categoryRepository->delete($cat));