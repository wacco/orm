<?php

use Nette\Templating\Helpers,
	Nette\Application\UI\Form,
	Nette\Forms\Container,
	Nette\Diagnostics\Debugger,
	Nette\Environment,
	DoctrineExtensions\NestedSet\Manager,
	Nette\Image;

Helpers::$dateFormat = '%d.%m.%Y @ %H:%M';
Container::extensionMethod('addDatePicker', 'Tools::addDatePicker');
Container::extensionMethod('addColorPicker', 'Tools::addColorPicker');
Container::extensionMethod('addPrice', 'Tools::addPrice');
Image::extensionMethod('resizeCrop', 'Tools::resizeCrop');
Image::extensionMethod('resizeCropCenter', 'Tools::resizeCropCenter');

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
	
	public static function resizeCrop($image, $width, $height, $center = false) {
		if ($image->width < $image->height) {
			$ratio = $width / $image->width;
			$mod = $image->height * $ratio < $height ? false : true;
		} else {
			$ratio = $height / $image->height;
			$mod = $image->width * $ratio < $width ? true : false;
		}

		if ($mod == true) {
			$image->resize($width, null);
			$offset = ($image->height - $height) / 2;
			$image->crop(0, $offset, $width, $height);
		} else {
			$image->resize(null, $height);
			$offset = ($image->width - $width) / 2;
			$image->crop($offset, 0, $width, $height);
		}

	    return $image;
	}


	public static function resizeCropCenter($image, $width, $height) {
		
		/*
		if (strpos($type, 'image/png') !== false) {
			$blank = Image::fromBlank($width, $height, Image::rgb(255, 255, 255, 127));
		} else {
			$blank = Image::fromBlank($width, $height, Image::rgb(255, 255, 255));
		}
		*/

		$blank = Image::fromBlank($width, $height, Image::rgb(255, 255, 255));
		
		$image->resize($width, $height);
		$left = ($width - $image->width) > 0 ? ($width - $image->width) / 2 : 0;
		$top = ($height - $image->height) > 0 ? ($height - $image->height) / 2 : 0;

		$blank->place($image, $left, $top, 100);
		
		return $blank;
	}
	
	public static function addDatePicker(Container $_this, $name, $label, $cols = NULL, $maxLength = NULL) {
		return $_this[$name] = new DatePicker($label, $cols, $maxLength);
	}

	public static function addColorPicker(Container $_this, $name, $label) {
		return $_this[$name] = new ColorPicker($label);
	}
	
	public static function addAjaxUpload(Container $_this, $name, $label, $cols = NULL, $maxLength = NULL) {
		return $_this[$name] = new AjaxUploadControl($label);
	}
	
	public static function addPrice(Container $_this, $name, $label, $cols = NULL, $maxLength = NULL) {
		return $_this[$name] = new Price($label, $cols, $maxLength);
	}

	public static function helperNumber($number, $digit = 0) {
		if (is_null($number)) {
			$number = 0;
		}
		return number_format((float)str_replace(',', '.', $number), $digit, ',', ' ');
	}
	
	public static function helperPrice(Nette\Database\Table\ActiveRow $object) {
		$price = new Extras\Types\Price((float)$object->price, (int)$object->tax);
		return $price;
	}
	
	public static function helperImage(Nette\Database\Table\ActiveRow $object, $type = null) {
		$storage = Environment::getConfig('storage');
		$images = Environment::getConfig('images');
		$baseUrl = Environment::getVariable('baseUrl');

		if (!isset($images->$type)) {
			throw new Nette\InvalidArgumentException("Typ obrazku `$type` neexistuje, pozri config.neon.");
		}
		$type = $images->$type;
		
		return "{$baseUrl}{$storage->publicPath}/{$object->id}-{$type->width}x{$type->height}.jpg?v=" . ($object->modified ? $object->modified->format('YmdHis') : 0);
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

class UploadedFileXhr {

	/**
	* Save the file to the specified path
	* @return boolean TRUE on success
	*/
	function save() {    
		$input = fopen("php://input", "r");
		$temp = tmpfile();
		$file = tempnam(sys_get_temp_dir(), "qqfile");
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);

		if ($realSize != $this->getSize()) {            
			return false;
		}

		$target = fopen($file, "w");        
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);

		return array(
			'name' => $this->getName(),
			'type' => Nette\Utils\MimeTypeDetector::fromFile($file),
			'size' => $this->getSize(),
			'tmp_name' => $file,
			'error' => UPLOAD_ERR_OK
		);
	}

	function getName() {
		return $_GET['qqfile'];
	}

	function getSize() {
		if (isset($_SERVER["CONTENT_LENGTH"])){
			return (int)$_SERVER["CONTENT_LENGTH"];            
		} else {
			throw new Exception('Getting content length is not supported.');
		}      
	}
}