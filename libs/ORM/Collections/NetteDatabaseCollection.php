<?php

namespace ORM\Collections;

use Nette\Database\Table\Selection, ORM\Mappers\IMapper;

class NetteDatabaseCollection extends ArrayCollection implements \Iterator {

	protected $selection;

	public function __construct(IMapper $mapper, Selection $selection) {
		$this->list = new \ArrayIterator;
		$this->mapper = $mapper;
		$this->selection = $selection;
	}

	public function rewind() {
		foreach ($this->selection as $row) {
			$entity = $this->mapper->load($row);
			$this->add($entity);
		}
		$this->list->rewind();
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

	public function count() {
		return $this->selection->count();
	}
}