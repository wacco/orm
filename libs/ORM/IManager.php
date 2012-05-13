<?php

namespace ORM;

/**
 * Manazer interface
 * @author Branislav Vaculčiak
 */
interface IManager {

	/** Vrati repozitar pre entitu */
	public function getRepository($entityName);
}