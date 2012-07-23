<?php
	get_function(array('dev','class_de','race_de','getlvbycolor'));
	
	$mysqli = new mysqli(DB_HOST, DB_USER,DB_PW, DB_DB);
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	$mysqli->set_charset("utf8");
?>