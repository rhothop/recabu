<?php
require_once('bd.php');
require_once('./postObject.php');
include_once('./drawer.php');
require_once('./languages.php');

function route($method, $urlData, $formData, $lang = 'ru_ru') {
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
		$resultStr = '<div class="row">
		<div class="col-0 col-lg-2"></div>
		<div class="col-12 col-lg-10">
		<form><input name="date" type="date" value="'.$date.'" /><input type="submit" value="Показать" /></form>
		</div>
		</div>';
		foreach ( $posts as $post ) {
			$resultStr .= drawPost( $post, false, $lang );
		}
		if(count($posts) == 0) {
			$langcl = new languages($lang);
		    $resultStr .= '<div class="row">
		<div class="col-0 col-lg-2"></div>
		<div class="col-12 col-lg-10">
		'.$langcl->dic['nofound'].'
		</div>
		</div>';
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