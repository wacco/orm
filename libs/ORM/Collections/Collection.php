<?php

namespace ORM\Collections;

use ORM\Mappers\IMapper;

class Collection implements \Iterator, \Countable {

	protected $list;
	protected $mapper;

	public function __construct(IMapper $mapper) {
		$this->list = new \ArrayIterator;
		$this->mapper = $mapper;
	}

	public function getIterator() {
		return $this;
	}

	public function getMapper() {
		return $this->mapper;
	}

	public function add($item) {
		$this->list->offsetSet($item->getId(), $item);
	}

	public function remove($item) {
		$this->list->offsetUnset($item->getId());
	}

	public function count() {
		return $this->list->count();
	}

	public function rewind() {
		$this->list->rewind();
	}

	public function current() {
		return $this->list->current();
	}

	public function key() {
		return $this->list->key();
	}

	public function next() {
		$this->list->next();
	}

	public function valid() {
		return $this->list->valid();
	}
}