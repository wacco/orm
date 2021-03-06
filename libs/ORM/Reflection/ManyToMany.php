<?php

namespace ORM\Reflection;

use Nette;

class ManyToMany extends Column {

	const NAME = 'manyToMany';

	public function getEntity() {
		return Entity::from($this->property->getDeclaringClass()->getName());
	}

	public function getTarget() {
		return $this->property->getAnnotation(static::NAME)->target;
	}

	public function getTargetClassName() {
		return $this->getEntityClassName($this->getTarget());
	}

	public function getTargetEntity() {
		return Entity::from($this->getTargetClassName());
	}

	public function getColumnName() {
		return $this->getName() . '_' . $this->getTargetEntity()->getPrimaryKey()->getName();
	}

	protected function getEntityClassName($class) {
		$parent = $this->property->getDeclaringClass();
		if (strpos($class, '\\') === 0) {
			return $class;
		}
		return $parent->getNamespaceName() . '\\' . $class;
	}

	public function getPairTable() {
		$table1 = Entity::from($this->property->getDeclaringClass()->getName())->getTableName();
		$table2 = $this->getTargetEntity()->getTableName();
		$tables = array($table1, $table2);
		sort($tables);
		return implode('_', $tables);
	}
}