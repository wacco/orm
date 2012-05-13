<?php

namespace Services;

use ORM, Entities;

/**
 * Readonly sluzba clankov
 * @author Branislav Vaculčiak
 */
class Article extends Base {

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->entity->getTitle();
	}

	/**
	 * @return string
	 */
	public function getContent() {
		return $this->entity->getContent();
	}

	/**
	 * @return Entities\Category
	 */
	public function getCategory() {
		return $this->entity->getCategory();
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->entity->getStatus();
	}
}