<?php
require_once('bd.php');
require_once('./postObject.php');

function route($method, $urlData, $formData, $lang = 'ru_ru') {
     
    if ($method === 'GET' && count($urlData) === 0) {
        if(isset($_COOKIE['auth'])) {
			return file_get_contents( './patterns/new_post_'.$lang.'.ptn' );
        } else {
            return 'Сначала авторизуйтесь';
        }
    } elseif ($method === 'POST' && count($urlData) === 0) {
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
		if(isset($formData['auth'])) {
			$result = $db->addPost($formData['title'], $formData['content'], $inNsfw, $inOc, $inParent, $formData['auth']);
		} else {
			$result = $db->addPost($formData['title'], $formData['content'], $inNsfw, $inOc, $inParent);
		}
		return $result;
	} elseif ($method === 'POST' && $urlData[0] === 'boyan') {
		$result = '';
		if(isset($formData['incoming'])) {
			$db = new bd();
			$result = $db->checkBoyan($formData['incoming']);
		}
		return $result;
	} elseif ($method === 'DELETE' && count($urlData) === 0) {
		$db = new bd();
		if(isset($formData['auth'])) {
			$result = $db->delPost($formData['post'], $formData['auth']);
		} else {
			$result = $db->delPost($formData['post'], $_COOKIE['auth']);
		}
		return $result;
	}
 
    header('HTTP/1.0 400 Bad Request');
	return '';
    return json_encode(array(
        'error' => 'Bad Request'
    ));
 
}
?>