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
	private $manyList = array();

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

		// nastavenie eventov
		foreach ($this->entityReflection->getEvents('ORM\Reflection\PreCreate') as $event) {
			$this->onBeforeCreate[] = function($item) use($event) {
				$item->{$event->getMethod()->getName()}();
			};
		}
		foreach ($this->entityReflection->getEvents('ORM\Reflection\PreUpdate') as $event) {
			$this->onBeforeUpdate[] = function($item) use($event) {
				$item->{$event->getMethod()->getName()}();
			};
		}
		foreach ($this->entityReflection->getEvents('ORM\Reflection\PostCreate') as $event) {
			$this->onAfterCreate[] = function($item) use($event) {
				$item->{$event->getMethod()->getName()}();
			};
		}
		foreach ($this->entityReflection->getEvents('ORM\Reflection\PostUpdate') as $event) {
			$this->onAfterUpdate[] = function($item) use($event) {
				$item->{$event->getMethod()->getName()}();
			};
		}
		foreach ($this->entityReflection->getEvents('ORM\Reflection\PreDelete') as $event) {
			$this->onBeforeDelete[] = function($item) use($event) {
				$item->{$event->getMethod()->getName()}();
			};
		}
		foreach ($this->entityReflection->getEvents('ORM\Reflection\PostDelete') as $event) {
			$this->onAfterDelete[] = function($item) use($event) {
				$item->{$event->getMethod()->getName()}();
			};
		}
	}

	public function getEntityReflection() {
		return $this->entityReflection;
	}

	public function getManager() {
		return $this->manager;
	}

	public function save(ORM\IEntity $entity) {
		if ($entity instanceof ORM\IProxy) {
			if ($entity->__isLoad() === false) {
				return $entity;
			}
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

		foreach ($this->entityReflection->getRelationships('ORM\Reflection\ManyToOne') as $column) {
			$value = $this->getValue($entity, $reflection, $column->getName());
			$column = $column->getTargetEntity()->getReferenceKeyName();
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
					$row = $this->connection->table($this->entityReflection->getTableName())->get($entity->getId());
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

		$toMany = $this->entityReflection->getRelationships('ORM\Reflection\ManyToMany')
			+ $this->entityReflection->getRelationships('ORM\Reflection\OneToMany');

		foreach ($toMany as $column) {
			$mapper = $this->getManager()->getRepository($column->getTargetClassName())->getMapper();
			$value = $this->getValue($entity, $reflection, $column->getName());
			$pairtable = $this->getPairTable($column->getTargetEntity()->getTableName(), $this->entityReflection->getTableName());

			if ($column->isInversedBy()) {
				// prejdem vsetky target entity a ulozim
				foreach ($value as $item) {
					$mapper->save($item, false);

					// spojenie z kazdou entitou (ak este nie je)
					if (get_class($value) == 'ORM\Collections\ArrayCollection' || !$mapper->getMany($value)->offsetExists($item->getId())) {
						$this->connection->table($pairtable)->insert(array(
							$this->entityReflection->getReferenceKeyName() => $entity->getId(),
							$column->getTargetEntity()->getReferenceKeyName() => $item->getId()
						));
					}
				}
			}
		}

		return $entity;
	}

	public function delete(ORM\IEntity $entity, $cascade = true) {
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

	public function findOneBy(array $values) {
		$data = $this->connection->table($this->entityReflection->getTableName())->where($values)->fetch();
		return $data ? $this->load($data) : false;
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
			$this->setValue(
				$entity, $reflection, $column->getName(),
				new ORM\Collections\PersistentCollection($this->manager, $entity, $column->getTargetClassName())
			);
		}
		foreach ($this->entityReflection->getRelationships('ORM\Reflection\ManyToMany') as $column) {
			$this->setValue(
				$entity, $reflection, $column->getName(),
				new ORM\Collections\PersistentCollection($this->manager, $entity, $column->getTargetClassName())
			);
		}
		foreach ($this->entityReflection->getRelationships('ORM\Reflection\ManyToOne') as $column) {
			$targetId = $column->getTargetEntity()->getReferenceKeyName();
			if (isset($data[$targetId])) {
				$mapper = $this->getManager()->getRepository($column->getTargetClassName())->getMapper();
				$targetEntity = $this->getProxyEntity($mapper, $column->getTargetClassName(), $data[$targetId]);
				$this->setValue($entity, $reflection, $column->getName(), $targetEntity);
			}
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



	private function getPairTable($table1, $table2) {
		$tables = array($table1, $table2);
		sort($tables);
		return implode('_', $tables);
	}

	public function getMany(ORM\Collections\PersistentCollection $collection) {
		$source = ORM\Reflection\Entity::from($collection->getParent());
		if (!isset($this->manyList[$source->getTableName()])) {
			
			$selection = $this->connection->table($this->entityReflection->getTableName())
				->where($this->getPairTable($source->getTableName(), $this->entityReflection->getTableName()) . ':' . $source->getReferenceKeyName())
				->where($source->getReferenceKeyName(), $collection->getParent()->getId());

			//TODO: vyriesit neduplikovanie entit
			$list = new \ArrayIterator;
			foreach ($selection as $row) {
				$entity = $this->load($row);
				$list->offsetSet($entity->getId(), $entity);
			}
			$this->manyList[$source->getTableName()] = $list;
		}
		return $this->manyList[$source->getTableName()];
	}
}