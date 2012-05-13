<?php

namespace ORM;

use Nette;

/**
 * Generator proxy entit
 * @author Branislav Vaculčiak
 */
class ProxyGenerator {

	const PREFIX = 'ORM\\Proxy\\';

	/**
	 * @var string
	 */
	private $entityDir = null;

	/**
	 * @var string
	 */
	private $proxyDir = null;

	/**
	 * @var Nette\Loaders\RobotLoader
	 */
	private $loader = null;

	/**
	 * @param string
	 * @param string
	 */
	public function __construct($entityDir, $proxyDir, $tempDir) {
		$this->entityDir = $entityDir;
		$this->proxyDir = $proxyDir;
		$this->loader = new Nette\Loaders\RobotLoader;
		$this->loader->setCacheStorage(new Nette\Caching\Storages\FileStorage($tempDir));
		$this->loader->addDirectory($this->entityDir)->register();
		if (!is_dir($this->proxyDir)) {
			$this->generate();
		}
		$this->loader->addDirectory($this->proxyDir)->register();
	}

	/**
	 * Vygeneruje proxy entity
	 */
	public function generate() {
		$indexedClasses = $this->loader->getIndexedClasses();
		foreach ($indexedClasses as $class => $file) {
			if (Reflection\Entity::from($class)->isEntity()) {
				$this->createProxyClass($class, $this->proxyDir);
			}
		}
	}

	/**
	 * Generuje PHP subor pre proxy entitu
	 * @param string
	 * @param string
	 */
	public function createProxyClass($class, $dir) {
		$class = Nette\Reflection\ClassType::from($class);
		$name = self::PREFIX . $class->getName();

		$documents = array();
		foreach ($class->getAnnotations() as $anotationName => $annotation) {
			$ann = array();
			foreach ($annotation[0] as $key => $value) {
				$ann[] = "$key=$value";
			}
			$documents[] = "@$anotationName(" . implode(', ', $ann) . ")";
		}
		
		$proxy = new Common\PHPClassType(trim(substr($name, strrpos($name, '\\')), '\\'));
		$proxy->setNamespace(substr($name, 0, strrpos($name, '\\')));
		$proxy->setImplements('\\ORM\\IProxy');
		$proxy->setDocuments($documents);
		$proxy->addExtend('\\' . $class->getName());

		$mapper = $proxy->addProperty('mapper');
		$mapper->setVisibility('private');

		$proxyMethod = $proxy->addMethod('__construct');
		$proxyMethod->addParameter('mapper')->setTypeHint('\\ORM\\Mappers\\IMapper');
		$proxyMethod->addParameter('primary');
		$proxyMethod->addBody('$this->_mapper = $mapper;');
		$proxyMethod->addBody('$this->_primary = $primary;');

		$proxyMethod = $proxy->addMethod('__load');
		$proxyMethod->addBody('$this->_mapper->loadProxy($this, $this->_primary);');

		$proxyMethod = $proxy->addMethod('__primary');
		$proxyMethod->addBody('return $this->_primary;');

		foreach ($class->getMethods() as $method) {
			if ($method->isConstructor()) {
				continue;
			}
			$args = array();
			$proxyMethod = $proxy->addMethod($method->getName());
			$proxyMethod->addBody('$this->__load();');

			foreach ($method->getParameters() as $parameter) {
				$args[] = '$' . $parameter->getName();
				if ($parameter->isDefaultValueAvailable()) {
					$proxyParam = $proxyMethod->addParameter($parameter->getName(), $parameter->getDefaultValue());
				} else {
					$proxyParam = $proxyMethod->addParameter($parameter->getName());
				}
				$proxyParam->setTypeHint($parameter->getClass() ? '\\' . $parameter->getClass()->name . ' ' : null);
			}
			$proxyMethod->addBody('return parent::' . $method->getName() . '(' . implode(', ', $args) . ');');
		}

		@mkdir($dir, 0777);
		file_put_contents($dir . '/' . str_replace('\\', '', $class->getName()) . '.php', "<?php\n\n" . $proxy);
	}
}