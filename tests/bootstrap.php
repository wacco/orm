<?php

/**
 * Test initialization and helpers.
 *
 * @author     David Grudl
 * @author     Branislav Vaculčiak
 * @package    Nette\Test
 */

// absolute filesystem path to the web root
define('ROOT_DIR', __DIR__ . '/..');
define('TESTS_DIR', ROOT_DIR . '/tests');

require TESTS_DIR . '/Test/TestHelpers.php';
require TESTS_DIR . '/Test/Assert.php';
require ROOT_DIR . '/libs/Nette/loader.php';
require __DIR__ . '/include/helpers.php';

// Load configuration from config.neon
$configurator = new Nette\Config\Configurator;
$configurator ->addParameters(array(
	'appDir' => ROOT_DIR
));
$configurator->setTempDirectory(ROOT_DIR . '/temp');
$configurator->addConfig(ROOT_DIR . '/config.neon');
$configurator->createRobotLoader()
	->addDirectory(ROOT_DIR . '/app')
	->addDirectory(ROOT_DIR . '/libs')
	->register();
$container = $configurator->createContainer();
$container->createProxyGenerator();


// configure environment
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', TRUE);
ini_set('html_errors', FALSE);
ini_set('log_errors', FALSE);

// catch unexpected errors/warnings/notices
set_error_handler(function($severity, $message, $file, $line) {
	if (($severity & error_reporting()) === $severity) {
		echo ("Error: $message in $file:$line");
		exit(TestCase::CODE_ERROR);
	}
	return FALSE;
});


$_SERVER = array_intersect_key($_SERVER, array_flip(array('PHP_SELF', 'SCRIPT_NAME', 'SERVER_ADDR', 'SERVER_SOFTWARE', 'HTTP_HOST', 'DOCUMENT_ROOT', 'OS')));
$_SERVER['REQUEST_TIME'] = 1234567890;
$_ENV = $_GET = $_POST = array();

if (PHP_SAPI !== 'cli') {
	header('Content-Type: text/plain; charset=utf-8');
}