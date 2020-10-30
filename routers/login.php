<?php
require_once('bd.php');
require_once('./postObject.php');

function route($method, $urlData, $formData, $lang = 'ru_ru') {
     
    if ($method === 'GET') {// && count($urlData) === 0) {
		//return file_get_contents( './new_post.ptn' );
    } elseif ($method === 'POST') {// && count($urlData) === 0) {
		$db = new bd();
		$result = $db->login($formData['login'], $formData['psw']);
		if($result === '') {
		    return '{"status":false,"content":"","msg":"Неверный логин или пароль"}';
		} else {
		    return '{"status":true,"content":"","msg":"'.$result.'"}';
        }
	} elseif ($method === 'DELETE') {// && count($urlData) === 0) {
		//setcookie ( 'auth', '', 0, '/', '', true, true, array([samesite]=>strict) );
		setcookie('auth', '', [
                        'path' => '/',
                        'domain' => 'recabu.cf',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'strict',
                    ]);
		return true;
	}
 
    header('HTTP/1.0 400 Bad Request');
	return '';
    return json_encode(array(
        'error' => 'Bad Request'
    ));
 
}
?>