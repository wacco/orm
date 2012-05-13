<?php

namespace Services\Admin;

use Services, Entities, Nette;

/**
 * Admin sluzba clankov
 * @author Branislav Vaculčiak
 */
class Article extends Services\Article {

	/**
	 * @param string
	 * @return Article
	 */
	public function setTitle($title) {
		return $this->entity->setTitle($title);
	}

	/**
	 * @param string
	 * @return Article
	 */
	public function setContent($content) {
		return $this->entity->setContent($content);
	}

	/**
	 * @param Entities\Category
	 * @return Article
	 */
	public function setCategory(Entities\Category $category = null) {
		return $this->entity->setCategory($category);
	}

	/**
	 * @param string
	 * @return Article
	 */
	public function setStatus($status) {
		return $this->entity->setStatus($status);
	}

	/**
	 * Ulozenie clanku
	 * @return Article
	 */
	public function save() {
		$this->entity->setCreated(new Nette\DateTime);
		$this->repository->save($this->entity);
	}

	/**
	 * Ulozenie a publikovanie clanku
	 * @return Article
	 */
	public function publish() {
		if ($this->entity->getStatus() != Entities\Article::STATUS_DRAFT) {
			throw new \Exception('Je možné publikovať len draft článku');
		}
		$this->entity->setStatus(Entities\Article::STATUS_PUBLISHED);
		//$this->entity->setPublished(new Nette\DateTime);
		$this->repository->save($this->entity);
	}
}