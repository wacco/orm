<?php

namespace ORM;

interface IRepository {

	public function save(IEntity $entity);

	public function delete(IEntity $entity);

	public function find($id);

	public function findBy(array $values);

	public function findAll();

	public function getMapper();
}