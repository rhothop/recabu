<?php
require_once('bd.php');
require_once('./postObject.php');
include_once('./drawer.php');

function route($method, $urlData, $formData) {
    if ($method === 'GET' && count($urlData) === 0) {
		$str = 1;
		if(isset($formData['str'])) {
			$str = $formData['str'];
		}
		$db = new bd();
		if(isset($formData['auth'])) {
			$token = $formData['auth'];
		} else {
			$token = $_COOKIE['auth'];
		}
		$posts = $db->getUnreadPage($token,$str);
		$resultStr = '';
		//foreach ( $posts as $post ) {
			$resultStr .= drawComments( $posts, $db->getUserByToken($token), false );
		//}
		
		$pages = $db->getAnswersCount($token);
		$resultStr .= '<div class="row">'.drawPageButton($pages,$str).'</div>';
		
		return $resultStr;
 
    }
 
    header('HTTP/1.0 400 Bad Request');
	return '';
    return json_encode(array(
        'error' => 'Bad Request'
    ));
 
}
?>