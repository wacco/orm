<?php

use Nette\Utils\Strings;

require __DIR__ . '/../bootstrap.php';

$manager = new ORM\Manager($container->database);
$repository = $manager->getRepository('Entities\Article');

$date = new Nette\DateTime;
$title = 'Moja titulka ' . Strings::random(4);
$content = 'Obrash stránky';

$article = new Entities\Article;
$article->setTitle($title);
$article->setContent($content);
$article->setCreated($date);

// ulozim entitu a repozitori vrati entitu
Assert::instance('Entities\Article', $repository->save($article));

// zistim ci manazer vracia rovnaku repozistory a nevytvara nove onjekty
Assert::match('%d%', $article->getId());

// zistim ci mi find vrati vytvorenu entitu
Assert::instance('Entities\Article', $repository->find($article->getId()));

// overim nasetovane a ulozene data
Assert::same($title, $article->getTitle());
Assert::same($content, $article->getContent());
Assert::equal($date, $article->getCreated());

// zistim ci repozitory vracia mapper
Assert::instance('ORM\Mappers\IMapper', $repository->getMapper());

// ulozim entitu a repozitori vrati entitu
Assert::true($repository->delete($article));