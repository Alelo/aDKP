<?php

function str_extract($sStr,$sStart,$sEnd, $debug = false){
		// Übergebener String wird in kleinbuchstaben konvertiert
		$sStr_low = strtolower($sStr);
		// Sucht die Entsprechenden Start und Stop positionen zum Extrahieren
		$pos_start = strpos($sStr_low,$sStart);
		$pos_end = strpos($sStr_low,$sEnd,($pos_start + strlen($sStart)));
		// Prüft nochmals ob sowohl Start- als auch Endposition vorhanden sind
		echo ($debug) ? $pos_start.'#' : '' ;
		if(($pos_start !== false) && ($pos_end !== false))
		{
			// Pos1 enthällt hiernach die 1.Position nach dem Suchstring
			$pos1 = $pos_start + strlen($sStart);
			$pos2 = $pos_end - $pos1;
			// Returnt den Substring
			return substr($sStr,$pos1,$pos2);
		}
		return false;
	}
?>