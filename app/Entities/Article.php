<?php

namespace Entities;

use ORM;

/**
 * @entity(table=article)
 * @repository(class=Repositories\ArticleRepository, mapper=ORM\Mappers\NetteDatabaseMapper)
 */
class Article extends Entity {

	const STATUS_DRAFT = 'draft';
	const STATUS_PUBLISHED = 'published';

	/**
	 * @column(type=string)
	 */
	protected $title;

	/**
	 * @column(type=text)
	 */
	protected $content;

	/**
	 * @column(type=string)
	 */
	protected $status = self::STATUS_DRAFT;

	/**
	 * @manyToMany(target=Tag)
	 */
	protected $tags;

	/**
	 * @manyToOne(target=Category)
	 */
	protected $category;

	public function __construct() {
		$this->tags = new ORM\Relationships\ManyToMany($this);
	}


	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setContent($content) {
		$this->content = $content;
		return $this;
	}

	public function getContent() {
		return $this->content;
	}

	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getCreated() {
		return $this->created;
	}

	public function addTag(Tag $tag) {
		$this->tags->add($tag);
		return $this;
	}

	public function removeTag(Tag $tag) {
		$this->tags->remove($tag);
		return $this;
	}

	public function getTags() {
		return $this->tags;
	}

	public function setCategory(Category $category) {
		$this->category = $category;
		return $this;
	}

	public function getCategory() {
		return $this->category;
	}
}