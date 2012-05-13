<?php

namespace Entities;

use ORM, Nette;

abstract class Entity implements ORM\IEntity, \IteratorAggregate {

	/**
	 * @primaryKey
	 * @column(type=integer)
	 */
	protected $id;

	/**
	 * @column(type=datetime)
	 */
	protected $created;

	/**
	 * @column(type=datetime)
	 */
	protected $updated;

	public function getIterator() {
		return new \ArrayIterator($this->toArray());
	}

	public function toArray() {
		$data = array();
		foreach (Nette\Reflection\ClassType::from($this)->getProperties() as $property) {
			$data[$property->getName()] = $this->{$property->getName()};
		}
		return $data;
	}

	/*
	 * Vrati hash objektu
	 * @return string
	 */
	public function getObjectHash() {
		return spl_object_hash($this);
	}

	public function getId() {
		return $this->id;
	}

	public function setCreated(\Nette\DateTime $date) {
		$this->created = $date;
		return $this;
	}

	public function getCreated() {
		return $this->created;
	}

	public function setUpdated(\Nette\DateTime $date) {
		$this->updated = $date;
		return $this;
	}

	public function getUpdated() {
		return $this->updated;
	}
}