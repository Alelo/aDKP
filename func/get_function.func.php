<?php
	function get_function($functionname){
		if(is_array($functionname)){
			foreach($functionname as $function){
				require_once('func/'.strtolower($function).'.func.php');
			}
		}else{
			require_once('func/'.strtolower($functionname).'.func.php');
		}
	}
?>