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
		$posts = $db->getUnreadPage($_COOKIE['auth'],$str);
		$resultStr = '';
		//foreach ( $posts as $post ) {
			$resultStr .= drawComments( $posts, $db->getUserByToken($_COOKIE['auth']), false );
		//}
		
		$pages = $db->getAnswersCount($_COOKIE['auth']);
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