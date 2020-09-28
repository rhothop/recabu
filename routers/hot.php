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
		$posts = $db->getHotPosts($str);
		$resultStr = '';
		foreach ( $posts as $post ) {
			$resultStr .= drawPost( $post );
		}
		
		$pages = $db->getNewpostPageCount();
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