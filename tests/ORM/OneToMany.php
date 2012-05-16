<?php

use Nette\Utils\Strings;

require __DIR__ . '/../bootstrap.php';

$manager = new ORM\Manager($container->database);
$categoryRepository = $manager->getRepository('Entities\Category');

$date = new Nette\DateTime;
$name = 'Kategória';
$title = 'Moja titulka ' . Strings::random(4);
$content = 'Obrash stránky';

$cat = new Entities\Category;
$cat->setName($name);
$cat->setCreated($date);
$categoryRepository->save($cat);

$cat1 = new Entities\Category;
$cat1->setName('Kategória ' . Strings::random(4));
$cat1->setCreated($date);
$cat1->setCategory($cat);
$categoryRepository->save($cat1);

$cat2 = new Entities\Category;
$cat2->setName('Kategória ' . Strings::random(4));
$cat2->setCreated($date);
$cat2->setCategory($cat);
$categoryRepository->save($cat2);

$cat3 = new Entities\Category;
$cat3->setName('Kategória ' . Strings::random(4));
$cat3->setCreated($date);
$cat3->setCategory($cat);
$categoryRepository->save($cat3);

$cat4 = new Entities\Category;
$cat4->setName('Kategória ' . Strings::random(4));
$cat4->setCreated($date);
$cat4->setCategory($cat);
$categoryRepository->save($cat4);


// overim zhodnu kategoriu / objekt
Assert::instance('ORM\Relationships\OneToMany', $cat->getChildren());

// overim zhodnu kategoriu / objekt
Assert::same(4, count($cat->getChildren()));

// zistim ci kategoria dostala ID
Assert::same($cat, $cat4->getCategory());

$cat4->setCategory(null);
$categoryRepository->save($cat4);

// overim zhodnu kategoriu / objekt
Assert::same(3, count($cat->getChildren()));

exit;
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