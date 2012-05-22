<?php

namespace Entities;

use ORM;

/**
 * @entity(table=tag)
 * @repository(class=ORM\Repository, mapper=ORM\Mappers\NetteDatabaseMapper)
 */
class Tag extends Entity {

	/**
	 * @column(type=varchar, null=false)
	 */
	protected $name;

	/**
	 * @manyToMany(targetEntity=Article, mappedBy="tags")
	 */
	protected $articles;

	public function __construct() {
		$this->articles = new ORM\Collections\ArrayCollection;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getName() {
		return $this->name;
	}
}