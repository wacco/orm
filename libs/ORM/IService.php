<?php

namespace ORM;

interface IService {

	public function __construct(IRepository $repository, IEntity $entity);
}