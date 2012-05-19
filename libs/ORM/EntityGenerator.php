<?php

namespace ORM;

use Nette;
use Nette\ArrayHash;
use Nette\Utils\Finder;
use Nette\Utils\Strings;

/**
 * Generator proxy entit
 * @author Dávid Ďurika
 */
class EntityGenerator {

	public function __construct($configDir, $destinationDir, $storage) {
		$this->loader = new Nette\Loaders\RobotLoader;
		$this->loader->setCacheStorage($storage);
		if (!is_dir($destinationDir)) {
			@mkdir($destinationDir, 0777);
		}
		$this->loader->addDirectory($destinationDir)->register();
			$this->generate($configDir, $destinationDir);
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
			// break;
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
		$property = $entity->addProperty($name);
		$property->visibility = 'protected';
		$property->name = $name;
		if(isset($config->defaultValue)) $property->value = $config->defaultValue;
		unset($config->propertyName, $config->defaultValue);
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

	/**
	 * [addMethodsToEntity description]
	 * @param [type] $code       [description]
	 * @param [type] $reflection [description]
	 * @param [type] $config     [description]
	 */
	protected function addMethodsToEntity($code, $reflection, $config) {

		$construct = $code->addMethod('__construct');

		foreach ($reflection->getColumns() as $propertyName => $column) {
			if(isset($config->class->properties->{$propertyName})) {
				$setter = $code->addMethod('set' . ucfirst($propertyName));
				$documents = array();
				$documents[] = "@param {$column->getType()} \$$propertyName";
				$setter->setDocuments($documents);
				$setter->addParameter($propertyName);
				$setter->addBody("\$this->$propertyName = \$$propertyName;");

				$getter = $code->addMethod('get' . ucfirst($propertyName));
				$documents = array();
				$documents[] = "@return {$column->getType()}";
				$getter->setDocuments($documents);
				$getter->addBody("return \$this->$propertyName;");
			}
		}
		
		foreach ($reflection->getRelationships() as $propertyName => $relationships) {
			// debug($config->class->properties);
			if(isset($config->class->properties->{$propertyName})) {
				if($relationships::TYPE == 'toMany') {
					$construct->addBody("\$this->$propertyName = new ORM\Collections\ArrayCollection;");
					
					$propertyNameSingular = $this->nameToSingular($propertyName);
					$setter = $code->addMethod('set' . ucfirst($propertyNameSingular));
					$documents = array();
					$documents[] = "@param {$relationships->getTargetClassName()} \$$propertyNameSingular";
					$setter->setDocuments($documents);
					$setter->addParameter($propertyNameSingular);
					$setter->addBody("\$this->{$propertyName}->add(\$$propertyNameSingular);");

					$propertyNameSingular = $this->nameToSingular($propertyName);
					$remover = $code->addMethod('remove' . ucfirst($propertyNameSingular));
					$documents = array();
					$documents[] = "@param {$relationships->getTargetClassName()} \$$propertyNameSingular";
					$remover->setDocuments($documents);
					$remover->addParameter($propertyNameSingular);
					$remover->addBody("\$this->{$propertyName}->remove(\$$propertyNameSingular);");


					$getter = $code->addMethod('get' . ucfirst($propertyName));
					$documents = array();
					$documents[] = "@return {$relationships->getTargetClassName()}";
					$getter->setDocuments($documents);
					$getter->addBody("return \$this->$propertyName;");
				} else {
					$setter = $code->addMethod('set' . ucfirst($propertyName));
					$documents = array();
					$documents[] = "@param {$relationships->getTargetClassName()} \$$propertyName";
					$setter->setDocuments($documents);
					$setter->addParameter($propertyName);
					$setter->addBody("\$this->{$propertyName} = \$$propertyName;");

					$getter = $code->addMethod('get' . ucfirst($propertyName));
					$documents = array();
					$documents[] = "@return {$relationships->getTargetClassName()}";
					$getter->setDocuments($documents);
					$getter->addBody("return \$this->$propertyName;");
				}
			}
		}
		echo "<pre>$code</pre>";
	}

	public function nameToPlural($name) {
		if(Strings::endsWith($name, 'y')) {
			return substr($name, 0, -1) . 'ies';
		} else {
			return $name . 's';
		}
	}

	public function nameToSingular($name) {
		$options = array(
			'tags' => 'tag',
		);
		$name = strtolower($name);
		if(isset($options[$name])) $name = $options[$name];
		else if(Strings::endsWith($name, 'ies')) {
			$name = substr($name, 0 , -3).'y';
		} else if (Strings::endsWith($name, 's')) {
			$name = substr($name, 0 , -1);
		}
		return $name;
	}

	protected function createFiles($entities, $destinationDir) {
		foreach ($entities as $name => $entity) {
			file_put_contents($destinationDir . '/' . str_replace('\\', '', $name) . '.php', "<?php\n\n" . $entity);
		}
	}
}
