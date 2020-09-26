<?php
define("ROOT_DIR",dirname(__FILE__).'/');

require_once "vendor/autoload.php"; //автозагрузчик классов
require_once "vendor/main.php"; //основной класс приложения

//$application = new Application();    
//$application->run();

$parsedown = new cebe\markdown\Markdown();
echo $parsedown->parse('Hello _Parsedown_!'); #  <p>Hello <em>Parsedown</em>!</p>
?>