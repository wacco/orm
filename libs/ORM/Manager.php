<?php

namespace ORM;

use Nette;

/**
 * Manazer repozitarov
 * @author Branislav Vaculčiak
 */
class Manager implements IManager {

	/**
	 * @var Nette\Database\Connection
	 */
	private $connection = null;

	/**
	 * @var Nette\DI\Container
	 */
	private $container = null;

	/**
	 * @var Nette\Caching\IStorage
	 */
	protected $cacheStorage = null;

	/**
	 * @param Nette\Database\Connection
	 */
	public function __construct(Nette\Database\Connection $connection, Nette\Caching\IStorage $cacheStorage = null) {
		//TODO: treba vyriesit zavyslost na databaze, pripadne na cache atd...
		//tak isto zavyslost na cacheStorage
		$this->connection = $connection;
		$this->container = new Nette\DI\Container;
		$this->cacheStorage = $cacheStorage;
	}

	/**
	 * Vrati prislusny repozitar podla entity
	 * @param string
	 * @return IRepository
	 */
	public function getRepository($entityName) {
		$table = Reflection\Entity::from($entityName);
		$repository = $table->hasRepository() ? $table->getRepository() : 'ORM\Repository';
		$mapper = $table->getMapper() ?: 'ORM\Mappers\NetteDatabaseMapper';
	
		if (!$this->container->hasService($table->getTableName())) {
			$mapper = new $mapper($this->connection, $this, $entityName);
			$repository = new $repository($mapper, $entityName);
			if ($this->cacheStorage instanceof Nette\Caching\IStorage) {
				$repository->setCacheStorage($this->cacheStorage);
			}
			$this->container->addService($table->getTableName(), $repository);
		}

		return $this->container->getService($table->getTableName());
	}
}