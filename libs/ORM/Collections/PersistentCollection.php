<?php

namespace ORM\Collections;

use ORM, ORM\Mappers\IMapper;

class PersistentCollection extends ArrayCollection {

	protected $manager;
	protected $target;
	private $isLoaded = false;

	public function __construct(ORM\IManager $manager, ORM\IEntity $parent, $target) {
		parent::__construct($parent);
		$this->parent = $parent;
		$this->target = $target;
		$this->manager = $manager;
	}

	public function getParent() {
		return $this->parent;
	}

	protected function lazyLoad() {
		if ($this->isLoaded === false) {
			$this->isLoaded = true;
			$this->list = $this->manager
				->getRepository($this->target)
				->getMapper()
				->getMany($this);
		}
	}
}