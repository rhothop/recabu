<?php
require_once('bd.php');
require_once('./postObject.php');
include_once('./drawer.php');

function route($method, $urlData, $formData) {
     
    if ($method === 'GET') {
        return file_get_contents( './patterns/rules.ptn' );
    }
}