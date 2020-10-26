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
	
	$resultData = route($method, $urlData, $formData);
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
	    
	    $pattern = file_get_contents('./patterns/pattern.ptn');
	    
	    $pattern = str_replace('%themetheme%', $style, $pattern);
	    $pattern = str_replace('%navbarclass%', $navbarclass, $pattern);

	    $pattern = str_replace('%checked1%', $check1, $pattern);
	    $pattern = str_replace('%checked2%', $check2, $pattern);
	    
	    $pattern = str_replace('%title%','Recabu',$pattern);
   	    $pattern = str_replace('%statusline%',drawTopLine(), $pattern);
		
		$pattern = str_replace('%toplist%',drawTopList(),$pattern);
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