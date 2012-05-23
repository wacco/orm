<?php

namespace ORM\Collections;

use ORM;

class PersistentCollection extends ArrayCollection {

	protected $manager;
	protected $target;
	protected $source;
	private $isLoaded = false;
	protected $news;

	public function __construct(ORM\IManager $manager, ORM\IEntity $parent, ORM\Reflection\ManyToMany $target) {
		parent::__construct($parent);
		$this->parent = $parent;
		$this->target = $target;
		$this->manager = $manager;
	}

	public function getParent() {
		return $this->parent;
	}

	public function getTarget() {
		return $this->target;
	}

	protected function lazyLoad() {
		if ($this->isLoaded === false) {
			$this->isLoaded = true;
			$this->list = $this->manager
				->getRepository($this->target->getTargetClassName())
				->getMapper()
				->getMany($this);
			$this->peristent = new \ArrayIterator($this->list->getArrayCopy());
		}
	}
}