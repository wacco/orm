<?php

namespace ORM\Mappers;

use ORM;

interface IMapper {

	public function save(ORM\IEntity $entity);

	public function delete(ORM\IEntity $entity);

	public function find($id);

	public function findBy(array $values);

	public function findOneBy(array $values);

	public function findAll();
}