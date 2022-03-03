<?php
class JSON {
	function decode($input, $assoc = 0){
		return JSON::__rawjsondecode($input, $assoc);
	}
	function encode($input){
		return JSON::__rawjsonencode($input);
	}
	function __decode($input, &$assoc){
		if(function_exists('json_decode'))
			$result = json_decode($input, $assoc);
		else {
			require_once('JSON.php');
			$json = new Services_JSON($assoc);
			$result = $json->decode($input);
		}
		return $result;
	}
	function __encode($input){
		if(function_exists('json_encode'))
			$result = json_encode($input);
		else {
			require_once('JSON.php');
			$json = new Services_JSON();
			$result = $json->encode($input);
		}
		return JSON::__rawjsonconvert($result);
	}
	function __rawjsondecode(&$input, &$assoc){
		return preg_match('#^(\[[^\a]+?\]|{[^\a]+?})$#', $input) ? JSON::__decode($input, $assoc) : array_shift(JSON::__decode('['.$input.']', $assoc));
	}
	function __rawjsonencode(&$input){
		if(is_array($input) || is_object($input))
			$input = JSON::__encode($input);
		elseif(is_string($input) || is_float($input) || is_int($input) || is_bool($input) || is_null($input))
			$input = substr(JSON::__encode(array($input)), 1, -1);
		else
			$input = 'null';
		return $input;
	}
	function __rawjsonconvert(&$str){
		$f = $r = array();
		foreach(array_merge(range(0, 7), array(11), range(14, 31)) as $v) {
			$f[] = chr($v);
			$r[] = "\\u00".sprintf("%02x", $v);
		}
		return str_replace($f, $r, $str);
	}
	
}