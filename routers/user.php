<?php
require_once('bd.php');
require_once('./postObject.php');
include_once('./drawer.php');

function route($method, $urlData, $formData, $lang = 'ru_ru') {
    if ($method === 'GET' && count($urlData) === 0) {
		$str = 1;
		if(isset($formData['str'])) {
			$str = $formData['str'];
		}
		$db = new bd();
		$posts = $db->getNewPosts($str);
		$resultStr = '';
		foreach ( $posts as $post ) {
			$resultStr .= drawPost( $post );
		}
		
		$pages = $db->getNewpostPageCount();
		$resultStr .= '<div class="row" style="justify-content:center;">'.drawPageButton($pages,$str).'</div>';
		
		return 'Not work';//$resultStr;
 
    }
    if($method === 'GET' && count($urlData) === 1) {
        $str = 1;
		if(isset($formData['str'])) {
			$str = $formData['str'];
		}
		$db = new bd();
		$posts = $db->getUserPosts($urlData[0],$str);
		$resultStr = '';
		foreach ( $posts as $post ) {
			$resultStr .= drawPost( $post );
		}
		
		$pages = $db->getUserPageCount($urlData[0]);
		$resultStr .= '<div class="row" style="justify-content:center;">'.drawPageButton($pages,$str).'</div>';
		
		return $resultStr;
    }
 
    header('HTTP/1.0 400 Bad Request');
	return '';
    return json_encode(array(
        'error' => 'Bad Request'
    ));
 
}
?>