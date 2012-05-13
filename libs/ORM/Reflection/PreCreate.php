<?php

namespace ORM\Reflection;

use Nette;

class PreCreate {

	const NAME = 'preCreate';

	protected $method;

	public function __construct(Nette\Reflection\Method $method) {
		$this->method = $method;
	}

	public static function from(Nette\Reflection\Method $method) {
		return new static($method);
	}

	public function getMethod() {
		return $this->method;
	}
}