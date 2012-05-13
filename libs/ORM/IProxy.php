<?php

namespace ORM;

/**
 * Proxy interface
 * @author Branislav Vaculčiak
 */
interface IProxy {

	public function __construct(Mappers\IMapper $mapper, $primary);
}