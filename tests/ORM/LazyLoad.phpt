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

// ulozim a nacitam
$articleRepository->save($article);
$article = $articleRepository->find($article->getId());

// zistim ci mi find vrati vytvorenu entitu
Assert::instance('ORM\Proxy\Entities\Category', $article->getCategory());

// overim ci natiahol data do proxy
Assert::same($name, $article->getCategory()->getName());

// vymazem clanok
Assert::true($articleRepository->delete($article));

// vymazem kategoriu
Assert::true($categoryRepository->delete($cat));