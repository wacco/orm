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

	public function getDefaultValue() {
		return isset($this->property->getAnnotation(static::NAME)->default)
			? $this->property->getAnnotation(static::NAME)->default
			: null;
	}

	public function getLength() {
		return isset($this->property->getAnnotation(static::NAME)->length)
			? (int)$this->property->getAnnotation(static::NAME)->length
			: null;
	}

	public function isNullable() {
		return isset($this->property->getAnnotation(static::NAME)->null)
			? (bool)$this->property->getAnnotation(static::NAME)->null
			: true;
	}

	public function isUnsigned() {
		return isset($this->property->getAnnotation(static::NAME)->unsigned)
			? (bool)$this->property->getAnnotation(static::NAME)->unsigned
			: false;
	}

	public function isPrimaryKey() {
		return $this->property->hasAnnotation(PrimaryKey::NAME);
	}
}