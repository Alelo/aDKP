<?php
	include_once('class/url_rewrite_header.class.php');
	require_once('func/get_function.func.php');
	require_once('config.php');
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if (strpos($ua, 'iPod') || strpos($ua, 'iPhone')){
		//iPod
		require_once('xhtml.php');//wird dan in html5 geaendert
	} else if(strpos($ua, 'Windows')){
		//PC
		require_once('xhtml.php');
	} else if(strpos($ua, 'iPad')){
		//iPad
		require_once('xhtml.php');//wird dan in html5wide geaendert
	} else {
		//fallback
		require_once('xhtml.php');
	}
?>