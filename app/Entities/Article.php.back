<?php

namespace Entities;

use Nette, ORM;

/**
 * @entity(table=article)
 * @repository(class=Repositories\ArticleRepository, mapper=ORM\Mappers\NetteDatabaseMapper)
 */
class Article extends Entity {

	const STATUS_DRAFT = 'draft';
	const STATUS_PUBLISHED = 'published';

	/**
	 * @column(type=varchar, null=false)
	 */
	protected $title;

	/**
	 * @column(type=text, null=false)
	 */
	protected $content;

	/**
	 * @column(type=varchar, length=20, null=false)
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

	/**
	 * @column(type=datetime)
	 */
	protected $published;

	public function __construct() {
		$this->tags = new ORM\Collections\ArrayCollection;
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

	public function setPublished(Nette\DateTime $date) {
		$this->published = $date;
		return $this;
	}

	public function getPublished() {
		return $this->updated;
	}
}