<?php

namespace ORM;

use Nette;

/**
 * Objekt repozitára
 * @author Branislav Vaculčiak
 */
class Repository implements IRepository {

	/**
	 * @var Mappers\IMapper
	 */
	private $mapper = null;

	/**
	 * @param Mappers\IMapper
	 * @param IManager
	 */
	public function __construct(Mappers\IMapper $mapper) {
		$this->mapper = $mapper;
	}

	/**
	 * Magicky finder
	 * @param string
	 * @param array
	 * @return mixed
	 */
	public function __call($name, $args) {
		if (strpos($name, 'findBy') === 0) {
			$param = strtolower(substr($name, 6));
			return $this->findBy(array($param => $args));
		}
		if (strpos($name, 'findOneBy') === 0) {
			$param = strtolower(substr($name, 9));
			return $this->findOneBy(array($param => $args));
		}
		throw new Nette\MemberAccessException("Call to undefined method " . get_class() . "::$name().");
	}

	/**
	 * Vrati pouzity mapper
	 * @return Mappers\IMapper
	 */
	public function getMapper() {
		return $this->mapper;
	}

	/**
	 * Vrati manazera repozitarov
	 * @return IManager
	 */
	public function getManager() {
		return $this->manager;
	}

	/**
	 * Ulozi entitu
	 * @param IEntity
	 */
	public function save(IEntity $item, $cascade = true) {
		return $this->mapper->save($item, $cascade);
	}

	/**
	 * Vymaze entitu
	 * @param IEntity
	 * @return bool
	 */
	public function delete(IEntity $item, $cascade = true) {
		return $this->mapper->delete($item, $cascade);
	}

	/**
	 * Vyhlda entitu podla ID
	 * @param int
	 * @return IEntity
	 */
	public function find($id) {
		return $this->mapper->find($id);
	}

	/**
	 * Vyhlada entity podla kriterii
	 * @param array
	 * @return Collection
	 */
	public function findBy(array $values) {
		return $this->mapper->findBy($values);
	}

	/**
	 * Vyhlada entitu podla kriterii
	 * @param array
	 * @return IEntity
	 */
	public function findOneBy(array $values) {
		return $this->mapper->findOneBy($values);
	}

	/**
	 * Vyhlada vsetky entity
	 * @return Collection
	 */
	public function findAll() {
		return $this->mapper->findAll();
	}
}