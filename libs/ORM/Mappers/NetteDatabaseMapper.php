<?php

namespace ORM\Mappers;

use Nette, ORM;

class NetteDatabaseMapper extends Nette\Object implements IMapper {

	/**
	 * @var Nette\Database\Connection
	 */
	private $connection = null;

	/**
	 * @var IManager
	 */
	private $manager = null;
	private $entityReflection;
	private $entityClass;
	private $stack = array();

	/** @var array */
	public $onBeforeCreate;

	/** @var array */
	public $onAfterCreate;

	/** @var array */
	public $onBeforeUpdate;

	/** @var array */
	public $onAfterUpdate;

	/** @var array */
	public $onBeforeDelete;

	/** @var array */
	public $onAfterDelete;

	public function __construct(Nette\Database\Connection $connection, ORM\IManager $manager, $entityClass) {
        if (strpos($entityClass, ORM\ProxyGenerator::PREFIX) !== false) {
			$entityClass = str_replace(ORM\ProxyGenerator::PREFIX, null, $entityClass);
        }

        $this->connection = $connection;
        $this->manager = $manager;
        $this->entityClass = $entityClass;
        $this->entityReflection = ORM\Reflection\Entity::from($entityClass);
	}

	public function getEntityReflection() {
		return $this->entityReflection;
	}

	public function getManager() {
		return $this->manager;
	}

	public function save(ORM\IEntity $entity) {
		if ($entity instanceof ORM\IProxy) {
			$entity = $this->loadProxy($entity, $entity->__primary());
		}
		if (!$entity instanceof $this->entityClass) {
			throw new ORM\Exceptions\Mapper("Do mapperu bola vlozena nespravna entita. Ocakavany typ {$this->entityClass}, vlozeny typ " . get_class($entity));
		}

		$data = array(); $old = array();
		$reflection = Nette\Reflection\ClassType::from($entity);
		$stackData = $this->findInStack($entity->getId());

		// ziskam lokalne data
		foreach ($this->entityReflection->getColumns() as $column) {
			if (!$column->isPrimaryKey()) {
				$data[$column->getName()] = $this->getValue($entity, $reflection, $column->getName());
				if ($stackData) {
					$old[$column->getName()] = $stackData->{$column->getName()};
				}
			}
		}

		// ziskam data pre vztah ManyToOne
		foreach ($this->entityReflection->getRelationships('ORM\Reflection\ManyToOne') as $column) {
			$value = $this->getValue($entity, $reflection, $column->getName());
			list($table, $column) = $this->connection->getDatabaseReflection()
				->getBelongsToReference($column->getName(), $column->getName());

			if ($value === null) {
				$data[$column] = null;
				if ($stackData) {
					$old[$column] = $stackData->{$column};
				}
			} elseif ($value instanceof ORM\IEntity) {
				$target = ORM\Reflection\Entity::from($value);
				$repository = $this->getManager()->getRepository($target->getClassName());
				$repository->save($value);

				$data[$column] = $value->getId();
				if ($stackData) {
					$old[$column] = $stackData->{$column};
				}
			}
		}

//debug($data);
//debug($data, $old);

		if ($entity->getId()) {
			if (sha1(serialize($data)) != sha1(serialize($old))) {
				$this->onBeforeUpdate($entity);
				$data = $this->updateData($data, $entity->toArray());
				if (!$row = $this->findInStack($entity->getId())) {
					$this->connection->table($this->entityReflection->getTableName())->get($entity->getId());
				}
				$item = $row->update($data);
				$this->onAfterUpdate($entity);
			}
		} else {
			$this->onBeforeCreate($entity);
			$data = $this->updateData($data, $entity->toArray());
			$item = $this->connection->table($this->entityReflection->getTableName())->insert($data);
			$this->setValue($entity, $reflection, 'id', $item->id);
			$this->onAfterCreate($entity);
		}
		if (isset($item) && $item instanceof Nette\Database\Table\ActiveRow) {
			$this->addToStack($item);
		}

		foreach ($this->entityReflection->getRelationships('ORM\Reflection\ManyToMany') as $column) {
			$mapper = $this->getManager()->getRepository($column->getTargetClassName())->getMapper();
			$value = $this->getValue($entity, $reflection, $column->getName());
			$value->setMapper($mapper)->connect($this->connection);
		}

		return $entity;
	}

	public function delete(ORM\IEntity $entity) {
		if (!$entity instanceof $this->entityClass) {
			throw new ORM\Exceptions\Mapper("Do mapperu bola vlozena nespravna entita. Ocakavany typ {$this->entityClass}, vlozeny typ " . get_class($entity));
		}
		$this->onBeforeDelete($entity);
		if ($stackData = $this->findInStack($entity->getId())) {
			$ret = (bool)$stackData->delete();
		} else {
			$ret = (bool)$this->connection->table($this->entityReflection->getTableName())->get($entity->getId())->delete();		
		}
		$this->onAfterDelete($entity);
		return $ret;
	}

	public function find($id) {
		if ($stackData = $this->findInStack($id)) {
			return $this->load($stackData);
		}
		if (!$data = $this->connection->table($this->entityReflection->getTableName())->get($id)) {
			throw new ORM\Exceptions\Mapper("Nenasiel som zaznam {$this->entityClass}($id)");
		}
		return $this->load($data);
	}

	public function findBy(array $values) {
		return $this->findAll()->where($values);
	}

	public function findAll() {
		return new ORM\Collections\NetteDatabaseCollection($this, $this->connection->table($this->entityReflection->getTableName()));
	}


	protected function updateData(array $before, array $after) {
		foreach ($before as $key => $value) {
			if (isset($after[$key]) && $before[$key] != $after[$key]) {
				$before[$key] = $after[$key];
			}
		}
		return $before;
	}

	protected function addToStack(Nette\Database\Table\ActiveRow $data) {
		$this->stack[$data->id] = $data;
	}

	protected function findInStack($id) {
		return isset($this->stack[$id]) ? $this->stack[$id] : false;
	}

	public function load(Nette\Database\Table\ActiveRow $data) {
		$class = $this->entityClass;
		$this->addToStack($data);
		$entity = new $class;
		$reflection = Nette\Reflection\ClassType::from($entity);

		foreach ($data as $property => $value) {
			$this->setValue($entity, $reflection, $property, $value);
		}

		foreach ($this->entityReflection->getRelationships('ORM\Reflection\ManyToOne') as $column) {
			$relationship = $this->getValue($entity, $reflection, $column->getName());

			list($table, $targetId) = $this->connection->getDatabaseReflection()
				->getBelongsToReference($this->entityReflection->getTableName(), $column->getTargetEntity()->getTableName());

			if (isset($data[$targetId])) {
				//$repository = $this->getManager()->getRepository($column->getTargetClassName());
				$mapper = $this->getManager()->getRepository($column->getTargetClassName())->getMapper();
				$targetEntity = $this->getProxyEntity($mapper, $column->getTargetClassName(), $data[$targetId]);
				//$targetEntity = $repository->find($data[$targetId]);
				$this->setValue($entity, $reflection, $column->getName(), $targetEntity);
			}
		}

		foreach ($this->entityReflection->getRelationships('ORM\Reflection\ManyToMany') as $column) {
			$relationship = $this->getValue($entity, $reflection, $column->getName());

			list($table, $sourceId) = $this->connection->getDatabaseReflection()
				->getHasManyReference($this->entityReflection->getTableName(), $column->getTargetEntity()->getTableName());

			$selection = $this->connection->table($column->getTargetEntity()->getTableName())
				->where($table . ':' . $sourceId)
				->where($sourceId, $entity->getId());

			$mapper = $this->getManager()->getRepository($column->getTargetClassName())->getMapper();
			$relationship->setMapper($mapper)->setSelection($selection);
		}

		foreach ($this->entityReflection->getRelationships('ORM\Reflection\OneToMany') as $column) {
			$relationship = $this->getValue($entity, $reflection, $column->getName());
			$selection = $data->related($column->getTargetEntity()->getTableName());
			$mapper = $this->getManager()->getRepository($column->getTargetClassName())->getMapper();
			$relationship->setMapper($mapper)->setSelection($selection);
		}

		return $entity;
	}

	private function getProxyEntity(IMapper $mapper, $entity, $primary) {
		$class = ORM\ProxyGenerator::PREFIX . trim($entity, '\\');
		return new $class($mapper, $primary);
	}

	public function loadProxy(ORM\IProxy $proxy, $primary) {
		$entity = $this->find($primary);
		$proxyReflection = Nette\Reflection\ClassType::from($proxy);
		$entityReflection = Nette\Reflection\ClassType::from($entity);

		foreach ($entityReflection->getProperties() as $property) {
			$value = $this->getValue($entity, $entityReflection, $property->getName());
			$this->setValue($proxy, $proxyReflection, $property->getName(), $value);
		}
		return $entity;
	}

	private function getValue(ORM\IEntity $entity, Nette\Reflection\ClassType $reflection, $key) {
		if (!$reflection->hasProperty($key)) {
			return;
		}
		$idProp = $reflection->getProperty($key);
		$idProp->setAccessible(TRUE);
		return $idProp->getValue($entity);
	}

	private function setValue(ORM\IEntity $entity, Nette\Reflection\ClassType $reflection, $key, $value) {
		if (!$reflection->hasProperty($key)) {
			return;
		}
		$idProp = $reflection->getProperty($key);
		$idProp->setAccessible(TRUE);
		$idProp->setValue($entity, $value);
	}
}