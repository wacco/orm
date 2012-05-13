<?php

namespace Entities;

use ORM, Nette;

abstract class Entity extends ORM\Entity {

	/**
	 * @column(type=datetime, null=false)
	 */
	protected $created;

	/**
	 * @column(type=datetime)
	 */
	protected $updated;

	public function setCreated(Nette\DateTime $date) {
		$this->created = $date;
		return $this;
	}

	public function getCreated() {
		return $this->created;
	}

	public function setUpdated(Nette\DateTime $date) {
		$this->updated = $date;
		return $this;
	}

	public function getUpdated() {
		return $this->updated;
	}

	/**
	 * @preCreate
	 */
	public function created() {
		$this->created = new Nette\DateTime;
		return $this;
	}

	/**
	 * @preCreate
	 * @preUpdate
	 */
	public function updated() {
		$this->updated = new Nette\DateTime;
		return $this;
	}
}