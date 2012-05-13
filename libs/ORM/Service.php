<?php

namespace ORM;

use Nette;

/**
 * Vrstva sluzieb
 * @author Branislav Vaculčiak
 */
class Service extends Nette\Object implements IService {

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

	/**
	 * Dynamicke zavolanie metod nad entitou
	 * @param string
	 * @param array
	 */
	public function __call($name, $args) {
		$class = new Nette\Reflection\ClassType($this->entity);

		if ($class->hasMethod($name)) {
			$method = $class->getMethod($name);
			$op1 = substr($method->getName(), 3);
			$op2 = substr($method->getName(), 5);
			$read = array('get' . $op1);
			$write = array('set' . $op1, 'add' . $op1, 'remove' . $op2);

			if ($this->getReflection()->hasAnnotation('access')) {
				$access = $this->getReflection()->getAnnotation('access');

				if (in_array($method->getName(), $write) && !in_array('write', (array)$access)) {
					throw new Nette\MemberAccessException("Call to not allowed method $class->name::$name().");
				}
				if (in_array($method->getName(), $read) && !in_array('read', (array)$access)) {
					throw new Nette\MemberAccessException("Call to not allowed method $class->name::$name().");
				}

				return $method->invokeArgs($this->entity, $args);
			} else {
				return $method->invokeArgs($this->entity, $args);
			}
		}
		throw new Nette\MemberAccessException("Call to undefined method $class->name::$name().");
	}
}