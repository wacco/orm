<?php

namespace ORM\Reflection;

use Nette;

class Entity {

	const NAME = 'entity';
	const REPOSITORY = 'repository';

	protected $class;

	public function __construct($class) {
		$this->class = Nette\Reflection\ClassType::from($class);
	}

	public static function from($class) {
		return new static($class);
	}

	public function isEntity() {
		return $this->class->hasAnnotation(static::NAME);
	}

	public function getTableName() {
		return $this->class->getAnnotation(static::NAME)->table;
	}

	public function getRepository() {
		return $this->class->getAnnotation(static::REPOSITORY)->class;
	}

	public function getMapper() {
		return $this->hasRepository() ? $this->class->getAnnotation(static::REPOSITORY)->mapper : false;
	}

	public function hasRepository() {
		return (bool)strlen($this->getRepository());
	}

	public function getClassName() {
		return $this->class->getName();
	}

	public function getPrimaryKey() {
		foreach ($this->class->getProperties() as $property) {
			if ($property->hasAnnotation(PrimaryKey::NAME)) {
				return PrimaryKey::from($property);
			}
		}
		return false;
	}

	public function getColumns() {
		$columns = array();
		foreach ($this->class->getProperties() as $property) {
			if ($property->hasAnnotation(Column::NAME)) {
				$columns[$property->getName()] = Column::from($property);
			}
		}
		return $columns;
	}

	public function getRelationships($type) {
		$columns = array();
		foreach ($this->class->getProperties() as $property) {
			if ($property->hasAnnotation($type::NAME)) {
				$columns[$property->getName()] = $type::from($property);
			}
		}
		return $columns;
	}

	public function getEvents($type) {
		$columns = array();
		foreach ($this->class->getMethods() as $method) {
			if ($method->hasAnnotation($type::NAME)) {
				$columns[$method->getName()] = $type::from($method);
			}
		}
		return $columns;
	}
}