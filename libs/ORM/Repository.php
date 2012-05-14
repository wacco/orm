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
	public function save(IEntity $item) {
		return $this->mapper->save($item);
	}

	/**
	 * Vymaze entitu
	 * @param IEntity
	 * @return bool
	 */
	public function delete(IEntity $item) {
		return $this->mapper->delete($item);
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
	 * Vyhlada vsetky entity
	 * @return Collection
	 */
	public function findAll() {
		return $this->mapper->findAll();
	}
}