<?php

namespace Entities;

use ORM;

/**
 * @entity(table=tag)
 * @repository(class=ORM\Repository, mapper=ORM\Mappers\NetteDatabaseMapper)
 */
class Tag extends Entity {

	/**
	 * @column(type=string)
	 */
	protected $name;

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getName() {
		return $this->name;
	}
}