<?php

use Nette\Utils\Strings;

require __DIR__ . '/../bootstrap.php';

$manager = new ORM\Manager($container->database);
$articleRepository = $manager->getRepository('Entities\Article');
$tagRepository = $manager->getRepository('Entities\Tag');

$date = new Nette\DateTime;
$title = 'Moja titulka ' . Strings::random(4);
$content = 'Obrash stránky';

$tag1 = new Entities\Tag;
$tag1->setName('tag1');
$tag1->setCreated($date);
$tag2 = new Entities\Tag;
$tag2->setName('tag2');
$tag2->setCreated($date);

$article = new Entities\Article;
$article->setTitle($title);
$article->setContent($content);
$article->setCreated($date);
$article->addTag($tag1);
$article->addTag($tag2);


// overim zhodnu kategoriu / objekt
Assert::instance('ORM\Collections\ArrayCollection', $article->getTags());

// overim zhodnu kategoriu / objekt
Assert::same(2, count($article->getTags()));

foreach ($article->getTags() as $tag) {
	// overim zhodny tag / objekt
	Assert::instance('Entities\Tag', $tag);
}

$articleRepository->save($article);

foreach ($article->getTags() as $tag) {
	// zistim ci tag dostal ID
	Assert::match('%d%', $tag->getId());
}

$article = $articleRepository->find($article->getId());

// overim zhodnu kategoriu / objekt
Assert::instance('ORM\Collections\PersistentCollection', $article->getTags());

// overim zhodnu kategoriu / objekt
Assert::same(2, count($article->getTags()));

$article->removeTag($tag1);

// overim zhodnu kategoriu / objekt
Assert::same(1, count($article->getTags()));

$article->removeTag($tag2);

// vymazem clanok
Assert::true($articleRepository->delete($article));