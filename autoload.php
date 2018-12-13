<?php

function __autoload($className) {

	$filePath = str_replace('\\', '/', $className);
	require_once ROOT_DIR .'/'. $filePath.'.php';
}



