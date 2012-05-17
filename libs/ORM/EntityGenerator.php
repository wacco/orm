<?php

namespace ORM;

use Nette;
use Nette\ArrayHash;
use Nette\Utils\Finder;

/**
 * Generator proxy entit
 * @author Dávid Ďurika
 */
class EntityGenerator {

	public function __construct($configDir, $destinationDir, $storage) {
		$this->loader = new Nette\Loaders\RobotLoader;
		$this->loader->setCacheStorage($storage);
		if (!is_dir($destinationDir)) {
		}
			$this->generate($configDir, $destinationDir);
		// $this->loader->addDirectory($destinationDir)->register();
	}

	protected function generate($configDir, $destinationDir) {
		$configs = $this->getConfig($configDir);
		$entities = array();
		foreach ($configs as $entityName => $config) {
			$entities[$entityName] = $this->createEnityClass($config);
		}
		$this->createFiles($entities, $destinationDir);
		foreach ($entities as $entityName => $entityCode) {
			$entityReflection = new Reflection\Entity($entityName);
			$entities[$entityName] = $this->addMethodsToEntity($entityCode, $entityReflection, $configs[$entityName]);
		}
	}

	protected function getConfig($configDir) {
		$configLoader = new \Nette\Config\Loader;
		$configs = new ArrayHash;

		foreach (Finder::findFiles('*.neon')->from($configDir) as $key => $value) {
			$config = ArrayHash::from($configLoader->load($key));
			$name = $this->formatEntityName($config);
			if(isset($configs[$name])) {
				throw new Exceptions\EntityGenerator("Ambiguous entity $name in file $key");
			}
			$configs[$this->formatEntityName($config)] = $config;
		}
		return $configs;
	}

	public function formatEntityName(ArrayHash $entityConfit) {
		return (isset($entityConfit->namespace) ? $entityConfit->namespace . '\\' : NULL) . $entityConfit->class->name;
	}

	public function createEnityClass($config) {
		$entity = new Common\PHPClassType($config->class->name);
		if(isset($config->namespace)) $entity->setNamespace($config->namespace);
		if(isset($config->use)) $entity->setTraits((array) $config->use);

		if(isset($config->class->annotations)) {
			$documents = array();
			foreach ((array) $config->class->annotations as $annotationType => $annotationValues) {
				$documents[] = $this->formatAnnotation($annotationType, $annotationValues);
			}
			$entity->setDocuments($documents);
		}

		if(isset($config->class->extends)) $entity->setExtends((array) $config->class->extends);
		if(isset($config->class->implements)) $entity->setImplements((array) $config->class->implements);

		if(isset($config->class->properties)) {
			foreach ($config->class->properties as $name => $propertyConfig) { 
				// debug($propertyConfig);
				$this->addPropertyToEntity($entity, $name, $propertyConfig);
			}
		}
		return $entity;
	}

	protected function addPropertyToEntity($entity, $name, $config) {
		$name = isset($config->propertyName) ? $config->propertyName : $name;
		$property = $entity->addProperty($name);
		$property->visibility = 'protected';
		$property->name = $name;
		if(isset($config->propertyValue)) $property->value = $config->propertyValue;
		unset($config->propertyName, $config->propertyValue);
		if(count($config)) {
			$documents = array();
			foreach ($config as $key => $value) {
				if($key == 'var') {
					$documents[] = "@var $value";
				} else if(is_numeric($key)) {
					$documents[] = $value;
				} else {
					$documents[] = $this->formatAnnotation($key, $value);
				}
			}
			$property->documents = $documents;
		}
	}

	protected function formatAnnotation($name, $values) {
		$annotations = array();
		if(isset($values)) {
			foreach ((array) $values as $key => $value) {
				if($value === FALSE) $value = 'FALSE';
				else if($value === TRUE) $value = 'TRUE';
				$annotations[] = "$key=$value";
			}
		}
		return "@$name(" . implode(', ', $annotations) . ")";
	}

	protected function addMethodsToEntity($code, $reflection, $config) {

		foreach ($reflection->getColumns() as $propertyName => $column) {
			if(isset($config->class->properties->{$propertyName})) {
				debug($column);
				# @todo dorobit docBlok pre setter a getter
				$setter = $code->addMethod('set' . ucfirst($propertyName));
				$setter->addParameter($propertyName);
				$setter->addBody("\$this->$propertyName = \$$propertyName;");

				$getter = $code->addMethod('get' . ucfirst($propertyName));
				$getter->addBody("return \$this->$propertyName;");
			}
		}
		
		foreach ($reflection->getRelationships() as $propertyName => $relationships) {
			debug($relationships->getTargetEntity());
			# @todo dorobit metody pre vstahove property
		}
		echo "<pre>$code</pre>";
	}

	protected function createFiles($entities, $destinationDir) {
		@mkdir($destinationDir, 0777);
		foreach ($entities as $name => $entity) {
			file_put_contents($destinationDir . '/' . str_replace('\\', '', $name) . '.php', "<?php\n\n" . $entity);
		}
	}
}
