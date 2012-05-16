<?php

namespace ORM\Common;

use Nette, Nette\Utils\PhpGenerator;

/**
 * Manazer repozitarov
 * @author Branislav Vaculčiak
 */
class PHPClassType extends PhpGenerator\ClassType {

	/** @var string */
	private $namespace = "";

	/**
	 * Set namespace name
	 * @param string
	 */
	public function setNamespace($namespace) {
		$this->namespace = $namespace;
	}

	/** @return string  PHP code */
	public function __toString() {
		$consts = array();
		foreach ($this->consts as $name => $value) {
			$consts[] = "const $name = " . PhpGenerator\Helpers::dump($value) . ";\n";
		}
		$properties = array();
		foreach ($this->properties as $property) {
			$properties[] = ($property->documents ? str_replace("\n", "\n * ", "/**\n" . implode("\n", (array) $property->documents)) . "\n */\n" : '')
				. $property->visibility . ' $' . $property->name
				. ($property->value === NULL ? '' : ' = ' . PhpGenerator\Helpers::dump($property->value))
				. ";\n";
		}
		return Nette\Utils\Strings::normalize(
			($this->namespace ? "namespace " . $this->namespace . ";\n\n" : null)
			. ($this->traits ? "use " . implode(', ', (array) $this->traits) . ";\n\n" : '')
			. ($this->documents ? str_replace("\n", "\n * ", "/**\n" . implode("\n", (array) $this->documents)) . "\n */\n" : '')
			. ($this->abstract ? 'abstract ' : '')
			. ($this->final ? 'final ' : '')
			. $this->type . ' '
			. $this->name . ' '
			. ($this->extends ? 'extends ' . implode(', ', (array) $this->extends) . ' ' : '')
			. ($this->implements ? 'implements ' . implode(', ', (array) $this->implements) . ' ' : '')
			. "\n{\n\n"
			. Nette\Utils\Strings::indent(
				($this->consts ? implode('', $consts) . "\n\n" : '')
				. ($this->properties ? implode("\n", $properties) . "\n\n" : '')
				. implode("\n\n\n", $this->methods), 1)
			. "\n\n}") . "\n";
	}
}