<?php
// Sammlung häufig genutzter oder praktischer Programmierfunktionen
// z.B. zum Debuggen

function fprint_r($string){
	echo '<pre>';
	print_r($string);
	echo '</pre>';
}

function dprint($string){
	if( isset($_GET['debug']) )
		echo '<b><font color="red">Debug:</font></b> '.$string .'<br>';
}

?>