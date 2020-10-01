<?php
require_once('bd.php');
require_once('./postObject.php');

function route($method, $urlData, $formData) {
     
    if ($method === 'POST' && count($urlData) === 0) {
		$db = new bd();
		$result = $db->votePost($formData['target'], $formData['value']);
		return $result;
	}
 
    header('HTTP/1.0 400 Bad Request');
	return '';
    return json_encode(array(
        'error' => 'Bad Request'
    ));
 
}
?>