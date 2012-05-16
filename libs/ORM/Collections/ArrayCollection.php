<?php

namespace ORM\Collections;

use ORM, ORM\Mappers\IMapper;

class ArrayCollection implements \Iterator, \Countable {

	protected $list;

	public function __construct() {
		$this->list = new \ArrayIterator;
	}

	public function getIterator() {
		return $this;
	}

	public function add(ORM\IEntity $entity) {
		$this->lazyLoad();
		if ($entity->getId() && $this->list->offsetExists($entity->getObjectHash())) {
			$this->list->offsetUnset($entity->getObjectHash());
		}
		$this->list->offsetSet($entity->getId() ?: $entity->getObjectHash(), $entity);
	}

	public function remove(ORM\IEntity $entity) {
		$this->lazyLoad();
		if ($this->list->offsetExists($entity->getId())) {
			$this->list->offsetUnset($entity->getId());
		}
		if ($this->list->offsetExists($entity->getObjectHash())) {
			$this->list->offsetUnset($entity->getObjectHash());
		}
	}

	public function count() {
		$this->lazyLoad();
		return $this->list->count();
	}

	public function rewind() {
		$this->lazyLoad();
		$this->list->rewind();
	}

	public function current() {
		$this->lazyLoad();
		return $this->list->current();
	}

	public function key() {
		$this->lazyLoad();
		return $this->list->key();
	}

	public function next() {
		$this->lazyLoad();
		$this->list->next();
	}

	public function valid() {
		$this->lazyLoad();
		return $this->list->valid();
	}

	protected function lazyLoad() {
	}
}