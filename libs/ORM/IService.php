<?php

namespace ORM;

/**
 * Service interface
 * @author Branislav Vaculčiak
 */
interface IService {

	public function __construct(IRepository $repository, IEntity $entity);
}