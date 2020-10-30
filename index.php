<?php
	require_once('drawer.php');
	$dataForShow = '';
	$method = $_SERVER['REQUEST_METHOD'];
	$formData = getFormData($method);
	
	$url = (isset($_GET['q'])) ? $_GET['q'] : '';
	$url = rtrim($url, '/');
	$urls = explode('/', $url);
	
	if( count($urls) == 1 && $urls[0] == '' ) {
	    header('Location: /new', true, 301 );
	    exit();
	}
	
	$router = $urls[0];
	$urlData = array_slice($urls, 1);
	
	$router_address = 'routers/' . $router . '.php';
	if( !file_exists( $router_address ) ) {
	    header("HTTP/1.0 404 Not Found");
	    echo 'Страница не найдена, азаза, четыре ноль четыре 404, аааааааа';
	    exit();
	}
	
	include_once( $router_address );
	
	$langs = ['ru','en'];
	$lang = 'ru_ru';
	if(isset($_COOKIE['lang'])) {
		$lang = $_COOKIE['lang'];
	} elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$lang_temp = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		if (in_array($lang_temp, $langs)) {
			$lang = $lang_temp.'_'.$lang_temp;
		}
	}

	$resultData = route($method, $urlData, $formData, $lang);
	$theme = 'dark';
	if(isset($_COOKIE['theme'])) {
	    $theme = $_COOKIE['theme'];
	}
	
	if( $method === 'GET' ) {
		
	    if($theme === 'dark') {
	        $style = '<link rel="stylesheet" href="/css/style_dark.css">';
	        $navbarclass = 'navbar-dark bg-dark';
	        $check1 = 'checked ';
	        $check2 = '';
	    } else {
	        $style = '<link rel="stylesheet" href="/css/style.css">';
	        $navbarclass = 'navbar-light bg-light';
	        $check1 = '';
	        $check2 = 'checked ';
	    }
		if($lang === 'en_en') {
	        $checklang1 = '';
	        $checklang2 = 'checked ';
	    } else {
	        $checklang1 = 'checked ';
	        $checklang2 = '';
	    }

	    
	    $pattern = file_get_contents('./patterns/pattern.ptn');
	    
	    $pattern = str_replace('%themetheme%', $style, $pattern);
	    $pattern = str_replace('%navbarclass%', $navbarclass, $pattern);
	    $pattern = str_replace('%navbarmenu%', drawNavBar($lang), $pattern);

	    $pattern = str_replace('%checked1%', $check1, $pattern);
	    $pattern = str_replace('%checked2%', $check2, $pattern);

	    $pattern = str_replace('%checkedlang1%', $checklang1, $pattern);
	    $pattern = str_replace('%checkedlang2%', $checklang2, $pattern);
	    
	    $pattern = str_replace('%title%','Recabu',$pattern);
   	    $pattern = str_replace('%statusline%',drawTopLine($lang), $pattern);
		
		$pattern = str_replace('%usermenu%',drawUserMenu($lang),$pattern);
		
		if($router != 'add' && $router != 'rules' ) {
			$pattern = str_replace('%toplist%',drawTopList($lang),$pattern);
		} else {
			$pattern = str_replace('%toplist%','',$pattern);
		}
		echo str_replace('%contcont%', $resultData, $pattern);
	} else {
		echo $resultData;
	}
	
	//include_once('pattern.ptn');

	function getFormData($method) {
		// GET или POST: данные возвращаем как есть
		if ($method === 'GET') return $_GET;
		if ($method === 'POST') return $_POST;
	 
		// PUT, PATCH или DELETE
		$data = array();
		$exploded = explode('&', file_get_contents('php://input'));
	 
		foreach($exploded as $pair) {
			$item = explode('=', $pair);
			if (count($item) == 2) {
				$data[urldecode($item[0])] = urldecode($item[1]);
			}
		}
	 
		return $data;
	}
?>