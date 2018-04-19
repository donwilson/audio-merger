<?php
	require_once(__DIR__ ."/config.php");
	
	try {
		if(!isset($_FILES['file']['error']) || (UPLOAD_ERR_OK !== $_FILES['file']['error'])) {
			throw new Exception("Upload failed");
		}
		
		if(!preg_match("#\.(mp3|wav)$#si", $_FILES['file']['name'], $ext_match)) {
			throw new Exception("File type not allowed");
		}
		
		// ready
		$file_ext = strtolower(trim($ext_match[1]));
		
		// generate random tmp name
		$attempts = 0;
		
		do {
			$tmp_filename = substr(md5($_FILES['file']['name'] ."_". time() ."_". microtime(true)), 0, 16) ."_". time() .".". $file_ext;
		} while(file_exists(TMP_DIR . $tmp_filename) && (++$attempts < MAX_TMPNAME_ATTEMPTS));
		
		if($attempts >= MAX_TMPNAME_ATTEMPTS) {
			throw new Exception("Unable to generate randomized filename");
		}
		
		// attempt to save file
		if(!move_uploaded_file($_FILES['file']['tmp_name'], TMP_DIR . $tmp_filename)) {
			throw new Exception("Unable to save uploaded file");
		}
		
		// success
		die_json('success', "Uploaded ". $_FILES['file']['name'], [
			'file_original' => $_FILES['file']['name'],
			'file_size' => filesize(TMP_DIR . $tmp_filename),
			'file_tmp' => $tmp_filename,
			'delete_key' => generateDeleteKeyForTmpFile($tmp_filename),
		]);
	} catch(Exception $e) {
		die_json('error', $e->getMessage());
	}