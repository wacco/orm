<?php

namespace ORM\Relationships;

use ORM;

interface IRelationship {

	public function add(ORM\IEntity $entity);

	public function remove(ORM\IEntity $entity);
}