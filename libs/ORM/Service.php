<?php

namespace ORM;

/**
 * Manazer repozitarov
 * @author Branislav Vaculčiak
 */
class Service implements IService {

	/**
	 * @var IRepository
	 */
	protected $repository = null;

	/**
	 * @var IEntity
	 */
	protected $entity = null;

	/**
	 * @param IRepository
	 * @param IEntity
	 */
	public function __construct(IRepository $repository, IEntity $entity) {
		$this->repository = $repository;
		$this->entity = $entity;
	}
}