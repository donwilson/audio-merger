<?php
	define('TMP_DIR', __DIR__ ."/tmp/");
	
	define('MAX_TMPNAME_ATTEMPTS', 10);
	
	define('DELETE_KEY_SALT', "yC8QxaS9833V3y2k");
	
	function generateDeleteKeyForTmpFile($rel_filename) {
		return md5( ((defined('DELETE_KEY_SALT') && DELETE_KEY_SALT)?DELETE_KEY_SALT:"") . md5_file(TMP_DIR . $rel_filename) );
	}
	
	function die_json($status="success", $message="", $cargo=[]) {
		header("Content-Type: application/json");
		
		die(json_encode([
			'status' => strtolower(trim($status)),
			'message' => $message,
			'cargo' => $cargo,
		]));
	}