<?php
require_once('bd.php');

function route($method, $urlData, $formData, $lang = 'ru_ru') {
    if ($method === 'POST' && count($urlData) === 1) {
		$db = new bd();

		if($urlData[0] === 'userlist') {
			return json_encode($db->getUserListByMask($formData['mask']),JSON_UNESCAPED_UNICODE);
		}
 
    }
 
    header('HTTP/1.0 400 Bad Request');
	return '';
    return json_encode(array(
        'error' => 'Bad Request'
    ));
 
}
?>