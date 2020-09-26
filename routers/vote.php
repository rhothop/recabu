<?php
require_once('bd.php');
require_once('./postObject.php');

function route($method, $urlData, $formData) {
     
    if ($method === 'GET' && count($urlData) === 0) {
		//return file_get_contents( './new_post.ptn' );
    } elseif ($method === 'POST' && count($urlData) === 0) {
		if(!isset($_COOKIE['auth'])) {
			return false;
		}
		$db = new bd();
		$result = $db->votePost($formData['target'], $formData['value']);
		return $result;
	} elseif ($method === 'DELETE' && count($urlData) === 0) {
		setcookie ( 'auth', '', 0, '/', '', false, false );
		return true;
	}
 
    header('HTTP/1.0 400 Bad Request');
	return '';
    return json_encode(array(
        'error' => 'Bad Request'
    ));
 
}
?>