<?php

namespace ORM;

use Nette;

/**
 * Generator sql struktury
 * @author Branislav Vaculčiak
 */
class SqlGenerator {

	/**
	 * @var string
	 */
	private $entityDir = null;

	/**
	 * @var Nette\Loaders\RobotLoader
	 */
	private $loader = null;

	/**
	 * @param string
	 */
	public function __construct($entityDir) {
		$this->entityDir = $entityDir;
		$this->loader = new Nette\Loaders\RobotLoader;
		$this->loader->setCacheStorage(new Nette\Caching\Storages\DevNullStorage);
		$this->loader->addDirectory($this->entityDir)->register();
	}

	/**
	 * Vygeneruje proxy entity
	 */
	public function generate($saveFile = null) {
		$indexedClasses = $this->loader->getIndexedClasses();
		$driver = new SqlDrivers\MySql;
		$output = $driver->getPrefix();
		foreach ($indexedClasses as $class => $file) {
			$entity = Reflection\Entity::from($class);
			if ($entity->isEntity()) {
				$driver->setEntity($entity);
				foreach ($entity->getColumns() as $column) {
					$driver->addColumn($column);
				}
				$output .= $driver->generate() . "\n\n";
			}
		}
		$output .= $driver->getPostfix();
		if ($saveFile && is_writable(dirname($saveFile))) {
			file_put_contents($saveFile, $output);
		} else {
			return $output;
		}
	}
}