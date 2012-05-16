<?php

namespace ORM\Mappers;

use ORM;

interface IMapper {

	public function save(ORM\IEntity $entity, $cascade = true);

	public function delete(ORM\IEntity $entity, $cascade = true);

	public function find($id);

	public function findBy(array $values);

	public function findOneBy(array $values);

	public function findAll();

	public function getMany(ORM\Collections\PersistentCollection $collection);
}