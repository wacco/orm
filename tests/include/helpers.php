<?php

function createTempImg() {
	$img = __DIR__ . '/img.jpg';
	$tmpname = tempnam(TEMP_DIR, "TEST");
	copy($img, $tmpname);
	$upload = array(
		'name' => basename($img),
		'type' => Nette\Utils\MimeTypeDetector::fromFile($tmpname),
		'size' => filesize($tmpname),
		'tmp_name' => $tmpname,
		'error' => UPLOAD_ERR_NO_FILE
	);
	return new Nette\Http\FileUpload($upload);
}

function debug() {
	$params = func_get_args();
	$message = "";
	foreach ((array) $params as $param) {
		$message .= "\n#  " . dumpVar($param);
	}
	$trace = debug_backtrace();
	$trace = end($trace);
	if (isset($trace['line'])) {
		$message .= "\n#  file: $trace[file]:$trace[line]";
	}
	echo "\n$message";
}

function dumpVar($var) {
	static $tableUtf, $tableBin, $reBinary = '#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{10FFFF}]#u';
	if ($tableUtf === NULL) {
		foreach (range("\x00", "\xFF") as $ch) {
			if (ord($ch) < 32 && strpos("\r\n\t", $ch) === FALSE) {
				$tableUtf[$ch] = $tableBin[$ch] = '\\x' . str_pad(dechex(ord($ch)), 2, '0', STR_PAD_LEFT);
			} elseif (ord($ch) < 127) {
				$tableUtf[$ch] = $tableBin[$ch] = $ch;
			} else {
				$tableUtf[$ch] = $ch; $tableBin[$ch] = '\\x' . dechex(ord($ch));
			}
		}
		$tableBin["\\"] = '\\\\';
		$tableBin["\r"] = '\\r';
		$tableBin["\n"] = '\\n';
		$tableBin["\t"] = '\\t';
		$tableUtf['\\x'] = $tableBin['\\x'] = '\\\\x';
	}

	if (is_bool($var)) {
		return $var ? 'TRUE' : 'FALSE';

	} elseif ($var === NULL) {
		return 'NULL';

	} elseif (is_int($var)) {
		return "$var";

	} elseif (is_float($var)) {
		$var = var_export($var, TRUE);
		return strpos($var, '.') === FALSE ? $var . '.0' : $var;

	} elseif (is_string($var)) {
		if ($cut = @iconv_strlen($var, 'UTF-8') > 100) {
			$var = iconv_substr($var, 0, 100, 'UTF-8');
		} elseif ($cut = strlen($var) > 100) {
			$var = substr($var, 0, 100);
		}
		return '"' . strtr($var, preg_match($reBinary, $var) || preg_last_error() ? $tableBin : $tableUtf) . '"' . ($cut ? ' ...' : '');

	} elseif (is_array($var)) {
		return "array(" . count($var) . ")";

	} elseif ($var instanceof Exception) {
		return 'Exception ' . get_class($var) . ': ' . ($var->getCode() ? '#' . $var->getCode() . ' ' : '') . $var->getMessage();

	} elseif (is_object($var)) {
		$arr = (array) $var;
		return "object(" . get_class($var) . ") (" . count($arr) . ")";

	} elseif (is_resource($var)) {
		return "resource(" . get_resource_type($var) . ")";

	} else {
		return "unknown type";
	}
}