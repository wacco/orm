<?php

namespace ORM\Relationships;

use Nette, ORM;

class ManyToMany extends Relationship {

	public function connect(Nette\Database\Connection $connection) {
		$source = ORM\Reflection\Entity::from($this->parent);

		foreach ($this->list as $child) {
			$target = $this->getTargetEntity();
			$entity = $this->mapper->save($child);

			if (!$this->wasLoad($child)) {
				list($table, $sourceId) = $connection->getDatabaseReflection()
					->getHasManyReference($source->getTableName(), $target->getTableName());

				list($table, $targetId) = $connection->getDatabaseReflection()
					->getHasManyReference($target->getTableName(), $source->getTableName());

				$connection->table($table)->insert(array(
					$sourceId => $this->parent->getId(),
					$targetId => $entity->getId(),
				));
			}
		}
	}

	public function getTargetEntity() {
		if ($this->mapper === null) {
			throw new \Exception("Nebol zvolený mapper");
		}
		return $this->mapper->getEntityReflection();
	}
}