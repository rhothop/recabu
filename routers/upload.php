<?php
require_once('bd.php');
require_once('./postObject.php');

function route($method, $urlData, $formData) {
     
    if ($method === 'POST') {
		$uploaddir = './uploads/';
		if(is_writable($uploaddir) && ( isset($_COOKIE['auth']) || isset( $formData['auth']))){ 
			$hashtime = md5(time());
			$upfile = $_FILES['img'];
			$newfilename = $hashtime.'_'.str_replace(' ','_',$upfile['name']);
			$newfile = $uploaddir.$newfilename;
			move_uploaded_file($upfile['tmp_name'],$newfile);
			return '{"result":true,"content":"/uploads/'.$newfilename.'","msg":""}';
		} else {
			return '{"result":false,"content":"","msg":"Уп-с, не получилось"}';
		}
	}
 
    header('HTTP/1.0 400 Bad Request');
	return '';
    return json_encode(array(
        'error' => 'Bad Request'
    ));
 
}
?>