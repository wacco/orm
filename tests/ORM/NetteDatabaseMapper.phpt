<?php

use Nette\Utils\Strings;

require __DIR__ . '/../bootstrap.php';

exit;

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










exit;

use MyServices as Services;

// vytvorim produkt
$product = new MyServices\Product;


/*
$file = createTempImg();

// vytvorim noveho vyrobcu
$brand = new Services\Brand;
$brand->setName('Výrobca-' . Strings::random(4));
$brand->save();

// vytvorim novu instranciu sevisy obrazku
$image = new AdminModule\Services\Image;
$image->setFile($file);

// vytvorim produkt
$product = new Services\Product;

// overim ci dostavam instanciu servisy
Assert::instance('Services\Product', $product);

// nastavim nazov
$product->setName('Test name');

// overim ci nastaveny nazov aj ziskam
Assert::same('Test name', $product->getName());

// nastavim vyrobcu
$product->setBrand($brand);

// overim ci nastaveny nazov aj ziskam
Assert::same($brand, $product->getBrand());

// pridam obrazok
$product->addImage($image);

// overim ci sa obrazok pridal
$images = $product->getImages();
Assert::instance('Services\Image', current($images));
Assert::same($image, current($images));

// ponastavujem ostatne veci
$product->setTitle('Simple test product');
$product->setShortText('Short text');
$product->setDescription('Product full description');
$product->setFlag('new');
$product->setPrice(new Extras\Types\Price(999.89, 20));

// overim ulozenie, musi vracat servisu
Assert::instance('Services\Product', $product->save());

// overim cas pridania
Assert::equal($product->getCreated(), new Nette\DateTime);

// vytvorim novu instranciu sevisy vyrobcu
$product = new Services\Product($product->getId());

// overim ci dostavam instanciu servisy
Assert::instance('Services\Product', $product);

// overim ci je stale ulozeny nazov
Assert::same('Test name', $product->getName());

// overim ci obrazok zoztal
$images = $product->getImages();
Assert::instance('Services\Image', current($images));
Assert::equal($image->getId(), current($images)->getId());

// vymazem veci
Assert::true($product->remove());
Assert::true($brand->remove());
Assert::true($image->remove());
unlink($file->getTemporaryFile());

*/

