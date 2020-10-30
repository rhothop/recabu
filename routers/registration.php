<?php
require_once('bd.php');
require_once('./postObject.php');
include_once('./drawer.php');

function route($method, $urlData, $formData, $lang = 'ru_ru') {
     
    if ($method === 'GET') {
        //return 'Регистрация пока отключена. Извините.';
        if(isset($_COOKIE['auth'])) {
			if($lang === 'en_en') {
				return 'You`re registered and authorized';
			} else {
				return 'Вы уже зарегистрированы и авторизованы';
			}
        } else {
			$content = file_get_contents( './patterns/registration_'.$lang.'.ptn' );
			if(isset($_GET['ref'])) {
				$content = str_replace('%refrefcode%',$_GET['ref'],$content);
			} else {
				$content = str_replace('%refrefcode%','',$content);
			}
            return $content;
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