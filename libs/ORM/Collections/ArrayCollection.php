<?php

namespace ORM;

use Nette;

class ArrayCollection implements \Iterator {

	private $list;

	public function __construct() {
		$this->list = new \ArrayIterator;
	}

	public function getIterator() {
		return $this;
	}

	public function add($item) {
		$this->list->offsetSet($item->getId(), $item);
	}

	public function remove($item) {
		$this->list->offsetUnset($item->getId());
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