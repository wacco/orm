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

	public function getReferenceKeyName() {
		return $this->getTableName() . '_id';
	}

	public function getPrimaryKey() {
		foreach ($this->class->getProperties() as $property) {
			if ($property->hasAnnotation(PrimaryKey::NAME)) {
				return PrimaryKey::from($property);
			}
		}
		return false;
	}

	public function getProperty($name) {
		if ($this->class->hasProperty($name)) {
			return $this->class->getProperty($name);
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
	/**
	 * 
	 * @param  array|string|NULL $filter
	 * @return array
	 */
	public function getRelationships($filter = NULL) {
		$columns = array();
		if($filter === NULL) {
			$filter = array(
				'ORM\Reflection\ManyToOne',
				'ORM\Reflection\ManyToMany',
				'ORM\Reflection\OneToMany',
				'ORM\Reflection\OneToOne',
			);
		}
		if(is_array($filter)) {
			foreach ($filter as $type) {
				$columns += $this->getRelationships($type);
			}
		} else {
			foreach ($this->class->getProperties() as $property) {
				if ($property->hasAnnotation($filter::NAME)) {
					$columns[$property->getName()] = $filter::from($property);
				}
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