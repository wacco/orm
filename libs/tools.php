<?php

use Nette\Templating\Helpers,
	Nette\Application\UI\Form,
	Nette\Forms\Container,
	Nette\Diagnostics\Debugger,
	Nette\Environment,
	DoctrineExtensions\NestedSet\Manager,
	Nette\Image;

Helpers::$dateFormat = '%d.%m.%Y @ %H:%M';

function debug() {
	Tools::dump(func_get_args());
}

class Tools {
	
	public static function dump() {
		$params = func_get_args();
		$trace = debug_backtrace();

		if (isset($params) && is_array($params)) {	
			foreach ($params as $array) {
				if (!Environment::getHttpRequest()->isAjax()) {
					Debugger::barDump($array, "{$trace[1]['file']} ({$trace[1]['line']})");
				} else {
					Debugger::fireLog($array);
				}
			}
		}
	}
	
	public static function getRemoteAddress() {
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
						return $ip;
					}
				}
			}
		}
	}
	
	public static function getExtension($string) {
		return substr($string, strrpos($string, '.')+1);
	}

	public static function downloadImage($url) {
		$tmpname = tempnam(TEMP_DIR, "WEBIMAGE");
		file_put_contents($tmpname, file_get_contents($url));
		$mime = Nette\Utils\MimeTypeDetector::fromFile($tmpname);

		if (strpos($mime, 'image/') === false) {
			return false;
		}

		$upload = array(
			'name' => basename($url),
			'type' => Nette\Utils\MimeTypeDetector::fromFile($tmpname),
			'size' => filesize($tmpname),
			'tmp_name' => $tmpname,
			'error' => UPLOAD_ERR_OK//UPLOAD_ERR_NO_FILE
		);
		return new Nette\Http\FileUpload($upload);
	}

	public static function createImage($tmpname) {
		if (!file_exists($tmpname)) {
			return false;
		}
		$mime = Nette\Utils\MimeTypeDetector::fromFile($tmpname);

		if (strpos($mime, 'image/') === false) {
			return false;
		}

		$upload = array(
			'name' => basename($tmpname),
			'type' => Nette\Utils\MimeTypeDetector::fromFile($tmpname),
			'size' => filesize($tmpname),
			'tmp_name' => $tmpname,
			'error' => UPLOAD_ERR_OK//UPLOAD_ERR_NO_FILE
		);
		return new Nette\Http\FileUpload($upload);
	}
}