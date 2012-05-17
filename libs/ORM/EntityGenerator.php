<?php

namespace ORM;

use Nette;
use Nette\ArrayHash;
use Nette\Utils\Finder;

/**
 * Generator proxy entit
 * @author David Ďurika
 */
class EntityGenerator {

	public function __construct($configDir, $destinationDir, $storage) {
		$this->loader = new Nette\Loaders\RobotLoader;
		$this->loader->setCacheStorage($storage);
		if (!is_dir($this->entityDir)) {
			$this->generate($configDir, $destinationDir);
		}
		$this->loader->addDirectory($this->entityDir)->register();
	}

	protected function generate($configDir, $destinationDir) {
		$configs = $this->getConfig($configDir);
		foreach ($configs as $entityName => $config) {
			$entityClass = $this->createEnityClass();
			$this->createFile($entityClass, $destinationDir);
		}
	}

	protected function getConfig($configDir) {
		$configLoader = new \Nette\Config\Loader;
		$configs = new ArrayHash;
		foreach (Finder::findFiles('.neon')->in($configDir) as $key => $value) {
			// $configLoader->load($this->settingsDir . '/presenters/' . $this->configName . '.neon', $this->configSection),
			$config = ArrayHash::from($configLoader->load($key));
			$configs[$this->formatEntityName($config)] = $config;
		}
		return $configs;
	}

	protected function formatEntityName(ArrayHash $entityConfit) {
		return (isset($entityConfit->namespace) ? $entityConfit->namespace . '\\' : NULL) . $entityConfit->class->name;
	}
}
