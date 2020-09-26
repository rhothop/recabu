<?php
require_once('bd.php');
require_once('./postObject.php');
include_once('./drawer.php');

function route($method, $urlData, $formData) {
     
    if ($method === 'GET' && count($urlData) === 1) {
        $action = $urlData[0];
 		$db = new bd();
		
		return 'done';
 
    }
 
    header('HTTP/1.0 400 Bad Request');
	return '';
    return json_encode(array(
        'error' => 'Bad Request'
    ));
 
}

?>