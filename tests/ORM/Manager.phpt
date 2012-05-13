<?php

use Nette\Utils\Strings;

require __DIR__ . '/../bootstrap.php';

$manager = new ORM\Manager($container->database);
$repository = $manager->getRepository('Entities\Article');

// zistim ci vrati spravnu repository (article ma nastavenu vlastnu)
Assert::instance('Repositories\ArticleRepository', $repository);

// zistim ci manazer vracia rovnaku repozistory a nevytvara nove onjekty
Assert::same($repository, $manager->getRepository('Entities\Article'));

// zistim ci pri category entite vraci standartnu repozitory 
Assert::instance('ORM\Repository', $manager->getRepository('Entities\Category'));

// zistim ci pri tag entite vraci standartnu repozitory 
Assert::instance('ORM\Repository', $manager->getRepository('Entities\Tag'));