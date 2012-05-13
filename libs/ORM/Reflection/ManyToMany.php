<?php

namespace ORM\Reflection;

use Nette;

class ManyToMany extends Column {

	const NAME = 'manyToMany';

	public function getTarget() {
		return $this->property->getAnnotation(static::NAME)->target;
	}

	public function getTargetClassName() {
		return $this->getEntityClassName($this->getTarget());
	}

	public function getTargetEntity() {
		return Entity::from($this->getTargetClassName());
	}

	protected function getEntityClassName($class) {
		$parent = $this->property->getDeclaringClass();
		if (strpos($class, '\\') === 0) {
			return $class;
		}
		return $parent->getNamespaceName() . '\\' . $class;
	}
}