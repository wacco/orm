<?php

namespace Services\Admin;

use ORM, Services, Entities, Nette;

/**
 * Admin sluzba clankov
 * @author Branislav Vaculčiak
 */
class Article extends Services\Article {

	/**
	 * Ulozenie clanku
	 * @return Article
	 */
	public function save() {
		$this->repository->save($this->entity);
	}

	/**
	 * Ulozenie a publikovanie clanku
	 * @return Article
	 */
	public function publish() {
		if ($this->entity->getStatus() != Entities\Article::STATUS_DRAFT) {
			throw new ORM\Exceptions\Service('Je možné publikovať len draft článku');
		}
		$this->entity->setStatus(Entities\Article::STATUS_PUBLISHED);
		$this->entity->setPublished(new Nette\DateTime);
		$this->repository->save($this->entity);
	}
}