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
		$entity = Reflection\Entity::from($item);
		foreach ($entity->getEvents('ORM\Reflection\PreCreate') as $event) {
			$this->mapper->onBeforeCreate[] = array($item, $event->getMethod()->getName());
		}
		foreach ($entity->getEvents('ORM\Reflection\PreUpdate') as $event) {
			$this->mapper->onBeforeUpdate[] = array($item, $event->getMethod()->getName());
		}

		$ret = $this->mapper->save($item);

		foreach ($entity->getEvents('ORM\Reflection\PostCreate') as $event) {
			$this->mapper->onAfterCreate[] = array($item, $event->getMethod()->getName());
		}
		foreach ($entity->getEvents('ORM\Reflection\PostUpdate') as $event) {
			$this->mapper->onAfterUpdate[] = array($item, $event->getMethod()->getName());
		}
		
		return $ret;
	}

	/**
	 * Vymaze entitu
	 * @param IEntity
	 * @return bool
	 */
	public function delete(IEntity $item) {
		$entity = Reflection\Entity::from($item);
		foreach ($entity->getEvents('ORM\Reflection\PreDelete') as $event) {
			$this->mapper->onBeforeDelete[] = array($item, $event->getMethod()->getName());
		}

		$ret = $this->mapper->delete($item);

		foreach ($entity->getEvents('ORM\Reflection\PostDelete') as $event) {
			$this->mapper->onAfterDelete[] = array($item, $event->getMethod()->getName());
		}

		return $ret;
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