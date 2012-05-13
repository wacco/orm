<?php

namespace ORM\Relationships;

use Nette, ORM;

abstract class Relationship implements IRelationship, \Iterator, \Countable {

	const NAME = 'manyToMany';

	protected $list = null;
	protected $loaded = array();
	protected $parent = null;
	protected $selection = null;
	protected $mapper = null;
	protected $isLoaded = false;

	public function __construct($parent) {
		$this->list = new \ArrayIterator;
		$this->parent = $parent;
	}

	public function setMapper(ORM\Mappers\Imapper $mapper) {
		$this->mapper = $mapper;
		return $this;
	}

	public function setSelection(Nette\Database\Table\Selection $selection) {
		$this->selection = $selection;
		return $this;
	}

	public function where($condition, $parameters = array()) {
		$this->selection->where($condition, $parameters);
		return $this;
	}

	public function orderBy($order) {
		$this->selection->order($order);
		return $this;
	}

	public function page($page, $itemsPerPage) {
		$this->selection->page($page, $itemsPerPage);
		return $this;
	}

	public function wasLoad(ORM\IEntity $entity) {
		return isset($this->loaded[$entity->getId()]);
	}

	public function getIterator() {
		return $this;
	}

	public function add(ORM\IEntity $entity) {
		if ($entity->getId() && $this->list->offsetExists($entity->getObjectHash())) {
			$this->list->offsetUnset($entity->getObjectHash());
		}
		$this->list->offsetSet($entity->getId() ?: $entity->getObjectHash(), $entity);
	}

	public function remove(ORM\IEntity $entity) {
		if ($this->list->offsetExists($entity->getId())) {
			$this->list->offsetUnset($entity->getId());
		}
		if ($this->list->offsetExists($entity->getObjectHash())) {
			$this->list->offsetUnset($entity->getObjectHash());
		}
	}

	public function load() {
		if ($this->isLoaded === true) {
			return;
		}
		if ($this->selection !== null) {
			foreach ($this->selection as $row) {
				$entity = $this->mapper->load($row);
				$this->loaded[$entity->getId()] = $entity;
				$this->add($entity);
			}
		}
		$this->isLoaded = true;
	}

	public function count() {
		$this->load();
		return $this->list->count();
	}

	public function rewind() {
		$this->load();
		$this->list->rewind();
	}

	public function current() {
		$this->load();
		return $this->list->current();
	}

	public function key() {
		return $this->list->key();
	}

	public function next() {
		$this->list->next();
	}

	public function valid() {
		$this->load();
		return $this->list->valid();
	}
}