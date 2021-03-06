<?php
require_once('bd.php');
require_once('./postObject.php');

function route($method, $urlData, $formData, $lang = 'ru_ru') {
     
    if ($method === 'POST' && count($urlData) === 0) {
		$db = new bd();
		if(isset($formData['auth'])) {
			$result = $db->votePost($formData['target'], $formData['value'], $formData['auth']);
		} else {
			$result = $db->votePost($formData['target'], $formData['value'], null);
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