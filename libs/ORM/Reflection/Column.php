<?php

namespace ORM\Reflection;

use Nette;

class Column {

	const NAME = 'column';

	protected $property;

	public function __construct(Nette\Reflection\Property $property) {
		$this->property = $property;
	}

	public static function from(Nette\Reflection\Property $property) {
		return new static($property);
	}

	public function getName() {
		return $this->property->getName();
	}

	public function getType() {
		return $this->property->getAnnotation(static::NAME)->type;
	}

	public function isPrimaryKey() {
		return $this->property->hasAnnotation(PrimaryKey::NAME);
	}
}