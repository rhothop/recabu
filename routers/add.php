<?php
require_once('bd.php');
require_once('./postObject.php');

function route($method, $urlData, $formData) {
     
    if ($method === 'GET' && count($urlData) === 0) {
        if(isset($_COOKIE['auth'])) {
            return file_get_contents( './patterns/new_post.ptn' );
        } else {
            return 'Сначала авторизуйтесь';
        }
    } elseif ($method === 'POST' && count($urlData) === 0) {
// 		if(!isset($_COOKIE['auth'])) {
// 			return false;
// 		}
		$inNsfw = false;
		$inOc = false;
		$inParent = 0;
		if( isset( $formData['nsfw'] ) ) {
			$inNsfw = $formData['nsfw'];
		}
		if( isset( $formData['oc'] ) ) {
			$inOc = $formData['oc'];
		}
		if( isset( $formData['parent'] ) ) {
			$inParent = $formData['parent'];
		}
		
		$db = new bd();
		$result = $db->addPost($formData['title'], $formData['content'], $inNsfw, $inOc, $inParent);
		return $result;
	}
 
    header('HTTP/1.0 400 Bad Request');
	return '';
    return json_encode(array(
        'error' => 'Bad Request'
    ));
 
}
?>