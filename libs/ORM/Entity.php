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

	/**
	 * Returns property value. Do not call directly.
	 * @param  string  property name
	 * @return mixed   property value
	 * @throws MemberAccessException if the property is not defined.
	 */
	public function &__get($name)
	{
		return Nette\ObjectMixin::get($this, $name);
	}



	/**
	 * Sets value of a property. Do not call directly.
	 * @param  string  property name
	 * @param  mixed   property value
	 * @return void
	 * @throws MemberAccessException if the property is not defined or is read-only
	 */
	public function __set($name, $value)
	{
		return Nette\ObjectMixin::set($this, $name, $value);
	}



	/**
	 * Is property defined?
	 * @param  string  property name
	 * @return bool
	 */
	public function __isset($name)
	{
		return Nette\ObjectMixin::has($this, $name);
	}



	/**
	 * Access to undeclared property.
	 * @param  string  property name
	 * @return void
	 * @throws MemberAccessException
	 */
	public function __unset($name)
	{
		Nette\ObjectMixin::remove($this, $name);
	}
}