<?php
	require_once(__DIR__ ."/config.php");
	
	$tmp_file = false;
	$tmp_file_path = false;
	
	try {
		if(!isset($_REQUEST['tmp_name']) || ("" === ($tmp_file = trim(urldecode($_REQUEST['tmp_name']))))) {
			throw new Exception("File not specified");
		}
		
		$tmp_file_path = realpath(TMP_DIR . $tmp_file);
		
		if(TMP_DIR !== substr($tmp_file_path, 0, strlen(TMP_DIR))) {
			throw new Exception("File name provided malformed");
		}
		
		$tmp_file = substr($tmp_file_path, strlen(TMP_DIR));
		
		if(!preg_match("#\.(mp3|wav)$#si", $tmp_file_path)) {
			throw new Exception("File type provided not allowed");
		}
		
		if(!file_exists($tmp_file_path)) {
			throw new Exception("File not found");
		}
		
		if(!isset($_REQUEST['delete_key']) || ($_REQUEST['delete_key'] !== generateDeleteKeyForTmpFile($tmp_file))) {
			throw new Exception("Delete key mismatch");
		}
		
		@unlink($tmp_file_path);
		
		die_json('success', "Successfully removed temporary file");
	} catch(Exception $e) {
		die_json('error', $e->getMessage(), [
			'tmp_file' => $tmp_file,
			'tmp_file_path' => $tmp_file_path,
		]);
	}