<?php

namespace ORM\SqlDrivers;

use ORM, Nette;

/**
 * Generator sql struktury
 * @author Branislav Vaculčiak
 */
class MySql {

	const AUTO_INCREMENT = 'AUTO_INCREMENT';
	const DEFAULT_WORD = 'DEFAULT';
	const ENGINE = 'ENGINE=InnoDB';
	const DEFAULT_INT_LENGTH = 10;
	const DEFAULT_STRING_LENGTH = 255;
	const DEFAULT_VALUE = 'NULL';
	const DEFAULT_CHARSET = 'utf8';
	const NOT_NULL = 'NOT NULL';
	const PRIMARY_KEY = 'PRIMARY KEY';
	const FOREIGN_KEY = 'FOREIGN KEY';
	const UNIQUE_KEY = 'UNIQUE KEY';
	const KEY = 'KEY';
	const CONSTRAINT = 'CONSTRAINT';
	const REFERENCES = 'REFERENCES';
	const UNSIGNED = 'unsigned';
	const CHARSET = 'CHARSET';
	const CASCADE_EVENT = 'ON DELETE CASCADE ON UPDATE CASCADE';

	/**
	 * @var string
	 */
	private $entity;

	/**
	 * @var Nette\Loaders\RobotLoader
	 */
	private $columns;

	/**
	 * @var array
	 */
	private $pairs = array();

	/**
	 * Vygeneruje proxy entity
	 */
	public function setEntity(ORM\Reflection\Entity $entity) {
		$this->entity = $entity;
	}

	/**
	 * Vygeneruje proxy entity
	 */
	public function addColumn(ORM\Reflection\Column $column) {
		$this->columns[] = $column;
	}

	/**
	 * Vygeneruje proxy entity
	 */
	public function generate() {
		$output = "DROP TABLE IF EXISTS " . $this->slash($this->entity->getTableName()) . ";\n\n";
		$output .= "CREATE TABLE " . $this->slash($this->entity->getTableName()) . " (\n";
		$columns = array();
		foreach ($this->entity->getColumns() as $column) {
			$columns[] = $this->slash($column->getName()) . " " . $this->generateColumnType($column);
		}
		foreach ($this->entity->getRelationships('ORM\Reflection\ManyToOne') as $column) {
			$columns[] = $this->slash($column->getColumnName()) . " " . $this->generateColumnType($column);
			$columns[] = $this->generateForeignKey($column->getName() . '_id', $column->getTargetEntity());
		}

		$output .= implode(",\n", $columns);
		$output .= ", \n" . self::PRIMARY_KEY . ' (' . $this->slash($this->entity->getPrimaryKey()->getName()) . ')';
		$output .= "\n) " . self::ENGINE . ' '
			. self::DEFAULT_WORD . ' '
			. self::CHARSET . ' '
			. self::DEFAULT_CHARSET . ';';

		foreach ($this->entity->getRelationships('ORM\Reflection\ManyToMany') as $column) {
			if (!in_array($column->getPairTable(), $this->pairs)) {
				array_push($this->pairs, $column->getPairTable());
				$pairTableName = $column->getPairTable();
				$pairTable = array();
				$pairTable[] = $this->slash('id') . ' int(10) unsigned NOT NULL AUTO_INCREMENT';
				$pairTable[] = $this->slash($column->getEntity()->getTableName() . '_id'). ' int(10) unsigned NOT NULL';
				$pairTable[] = $this->slash($column->getTargetEntity()->getTableName() . '_id') . ' int(10) unsigned NOT NULL';
				$pairTable[] = self::PRIMARY_KEY . ' (' . $this->slash('id') . ')';
				$pairTable[] = self::UNIQUE_KEY . ' ' . $this->slash($column->getPairTable())
					. ' (' . $this->slash($column->getTargetEntity()->getTableName() . '_id')
					. ',' . $this->slash($column->getEntity()->getTableName() . '_id') . ')';
				$pairTable[] = $this->generateForeignKey($column->getEntity()->getTableName() . '_id', $column->getEntity());
				$pairTable[] = $this->generateForeignKey($column->getTargetEntity()->getTableName() . '_id', $column->getTargetEntity());
				
				$output .= "\n\nDROP TABLE IF EXISTS " . $this->slash($pairTableName) . ";\n\n";
				$output .= "CREATE TABLE " . $this->slash($pairTableName) . " (\n";
				$output .= implode(",\n", $pairTable);
				$output .= "\n) " . self::ENGINE . ' '
					. self::DEFAULT_WORD . ' '
					. self::CHARSET . ' '
					. self::DEFAULT_CHARSET . ';';
			}
		}

		return $output;
	}

	/**
	 * Vygeneruje proxy entity
	 */
	public function getPrefix() {
		return "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n"
			. "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n"
			. "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n"
			. "/*!40101 SET NAMES utf8 */;\n"
			. "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n"
			. "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n"
			. "/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n\n\n\n";
	}

	/**
	 * Vygeneruje proxy entity
	 */
	public function getPostfix() {
		return "\n\n/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;\n"
			. "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n"
			. "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n"
			. "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n"
			. "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n"
			. "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";
	}

	private function generateForeignKey($reference, $target) {
		return self::KEY . ' ' . $this->slash($reference) . ' (' . $this->slash($reference) . "),\n"
			. self::CONSTRAINT . ' ' . $this->slash($target->getTableName() . Nette\Utils\Strings::random(4, '0-9')) . ' '
			. self::FOREIGN_KEY . ' (' . $this->slash($reference) . ') '
			. self::REFERENCES .  ' ' . $this->slash($target->getTableName())
			. ' (' . $this->slash($target->getPrimaryKey()->getName()) . ') '
			. self::CASCADE_EVENT;
	}

	private function generateColumnType($column) {
		if ($column instanceof ORM\Reflection\ManyToOne) {
			return 'integer'
				. '(' . ($column->getLength() ? $column->getLength() : self::DEFAULT_INT_LENGTH) . ')'
				. ' ' . self::UNSIGNED;
		}
		switch ($column->getType()) {
			case 'integer':
				return 'int'
					. '(' . ($column->getLength() ? $column->getLength() : self::DEFAULT_INT_LENGTH) . ')'
					. ($column->isUnsigned() ? ' ' . self::UNSIGNED : null)
					. ($column->isNullable() ? null : ' ' . self::NOT_NULL)
					. ($column->isPrimaryKey() ? ' ' . self::AUTO_INCREMENT : null);
			case 'bool':
				return 'tinyint(1) ' . self::UNSIGNED . " DEFAULT '0'";
			case 'float':
				return 'float'
					. '(' . ($column->getLength() ? $column->getLength() : self::DEFAULT_INT_LENGTH) . ')'
					. ($column->isUnsigned() ? ' ' . self::UNSIGNED : null)
					. ($column->isNullable() ? null : ' ' . self::NOT_NULL)
					. ($column->isPrimaryKey() ? ' ' . self::AUTO_INCREMENT : null);
			case 'varchar':
				return 'varchar' . '(' . ($column->getLength() ? $column->getLength() : self::DEFAULT_STRING_LENGTH) . ')'
					. ($column->isNullable() ? null : ' ' . self::NOT_NULL)
					. ($column->isNullable() ? ' ' . self::DEFAULT_WORD . ' ' . ($column->getDefaultValue() === null ? self::DEFAULT_VALUE : "'{$column->getDefaultValue()}'") : null);
			case 'string':
				return 'varchar' . '(' . ($column->getLength() ? $column->getLength() : self::DEFAULT_STRING_LENGTH) . ')'
					. ($column->isNullable() ? null : ' ' . self::NOT_NULL)
					. ($column->isNullable() ? ' ' . self::DEFAULT_WORD . ' ' . ($column->getDefaultValue() === null ? self::DEFAULT_VALUE : "'{$column->getDefaultValue()}'") : null);
			case 'datetime':
				return 'datetime'
					. ($column->isNullable() ? null : ' ' . self::NOT_NULL)
					. ($column->isNullable() ? ' ' . self::DEFAULT_WORD . ' ' . ($column->getDefaultValue() === null ? self::DEFAULT_VALUE : "'{$column->getDefaultValue()}'") : null);
			case 'text':
				return 'text'
					. ($column->isNullable() ? null : ' ' . self::NOT_NULL)
					. ($column->isNullable() ? ' ' . self::DEFAULT_WORD . ' ' . ($column->getDefaultValue() === null ? self::DEFAULT_VALUE : "'{$column->getDefaultValue()}'") : null);
			case 'longtext':
				return 'longtext'
					. ($column->isNullable() ? null : ' ' . self::NOT_NULL)
					. ($column->isNullable() ? ' ' . self::DEFAULT_WORD . ' ' . ($column->getDefaultValue() === null ? self::DEFAULT_VALUE : "'{$column->getDefaultValue()}'") : null);

		}
	}

	private function slash($string) {
		return "`{$string}`";
	}

	public function __toString() {
		return $this->generate();
	}
}