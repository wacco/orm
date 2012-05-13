<?php

namespace ORM;

interface IProxy {

	public function __construct(Mappers\IMapper $mapper, $primary);
}