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
		$date = date('Y-m-d');
		if(isset($formData['date'])) {
			$date = $formData['date'];
		}
		$db = new bd();
		$posts = $db->getTopPosts($str,$date);
		$resultStr = '<form><input name="date" type="date" value="'.$date.'" /><input type="submit" value="Показать" /></form>';
		foreach ( $posts as $post ) {
			$resultStr .= drawPost( $post );
		}
		if(count($posts) == 0) {
		    $resultStr .= 'Упс, ничего нет';
		}
		
		$pages = $db->getToppostPageCount($date);
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