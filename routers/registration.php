<?php
require_once('bd.php');
require_once('./postObject.php');
include_once('./drawer.php');

function route($method, $urlData, $formData) {
     
    if ($method === 'GET') {
        //return 'Регистрация пока отключена. Извините.';
        if(isset($_COOKIE['auth'])) {
            return 'Вы уже зарегистрированы и авторизованы';
        } else {
            return file_get_contents( './patterns/registration.ptn' );
        }
    }
    
    if ($method === 'POST') {
        $bd = new bd();
        return $bd->regNewUser($formData['login'],$formData['pass'],$formData['ref']);
    }
 
    header('HTTP/1.0 400 Bad Request');
	return '';
    return json_encode(array(
        'error' => 'Bad Request'
    ));
 
}

?>