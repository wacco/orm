<?php

namespace ORM;

use Nette;

/**
 * Zakladny objekt entity
 * @author Branislav Vaculčiak
 */
abstract class Entity implements IEntity, \IteratorAggregate {

	/**
	 * @primaryKey
	 * @column(type=integer, unsigned=true, null=false)
	 */
	protected $id;

	/**
	 * Vrati iterator objektu
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new \ArrayIterator($this->toArray());
	}

	/**
	 * Vrati data objektu v poli
	 * @return array
	 */
	public function toArray() {
		$data = array();
		foreach (Nette\Reflection\ClassType::from($this)->getProperties() as $property) {
			$data[$property->getName()] = $this->{$property->getName()};
		}
		return $data;
	}

	/**
	 * Vrati hash objektu
	 * @return string
	 */
	public function getObjectHash() {
		return spl_object_hash($this);
	}

	/*
	 * Vrati ID objektu
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}
}