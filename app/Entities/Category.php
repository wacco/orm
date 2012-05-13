<?php

namespace Entities;

use ORM;

/**
 * @entity(table=category)
 * @repository(class=ORM\Repository, mapper=ORM\Mappers\NetteDatabaseMapper)
 */
class Category extends Entity {

	/**
	 * @column(type=varchar, null=false)
	 */
	protected $name;

	/**
	 * @manyToOne(target=Category)
	 */
	protected $category;

	/**
	 * @oneToMany(target=Category)
	 */
	protected $children;

	/**
	 * @oneToMany(target=Article)
	 */
	protected $articles;

	public function __construct() {
		$this->children = new ORM\Relationships\OneToMany($this);
		$this->articles = new ORM\Relationships\OneToMany($this);
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setCategory(Category $category = null) {
		if ($category === null && $this->category) {
			$this->category->removeChild($this);
		} else {
			$category->addChild($this);
		}
		$this->category = $category;
		return $this;
	}

	public function getCategory() {
		return $this->category;
	}

	public function addChild(Category $category) {
		$this->children->add($category);
		return $this;
	}

	public function removeChild(Category $category) {
		$this->children->remove($category);
		return $this;
	}

	public function getChildren() {
		return $this->children;
	}

	public function getArticles() {
		return $this->articles;
	}
}