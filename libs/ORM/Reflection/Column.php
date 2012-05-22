<?php

namespace ORM\Reflection;

use Nette;

class Column {

	const NAME = 'column';
	const TYPE = 'type';
	const DEFAULTS = 'default';
	const LENGTH = 'length';
	const NULL = 'null';
	const UNSIGNED = 'unsigned';

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
		return $this->property->getAnnotation(static::NAME)->{static::TYPE};
	}

	public function getDefaultValue() {
		return isset($this->property->getAnnotation(static::NAME)->{static::DEFAULTS})
			? $this->property->getAnnotation(static::NAME)->{static::DEFAULTS}
			: null;
	}

	public function getLength() {
		return isset($this->property->getAnnotation(static::NAME)->{static::LENGTH})
			? (int)$this->property->getAnnotation(static::NAME)->{static::LENGTH}
			: null;
	}

	public function isNullable() {
		return isset($this->property->getAnnotation(static::NAME)->{static::NULL})
			? (bool)$this->property->getAnnotation(static::NAME)->{static::NULL}
			: true;
	}

	public function isUnsigned() {
		return isset($this->property->getAnnotation(static::NAME)->{static::UNSIGNED})
			? (bool)$this->property->getAnnotation(static::NAME)->{static::UNSIGNED}
			: false;
	}

	public function isPrimaryKey() {
		return $this->property->hasAnnotation(PrimaryKey::NAME);
	}
}