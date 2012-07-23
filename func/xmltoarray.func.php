﻿<?php
	function xmltoarray($arrObjData, $arrSkipIndices = array()){
		$arrData = array();
		// if input is object, convert into array
		if (is_object($arrObjData)) {
			$arrObjData = get_object_vars($arrObjData);
		}
	
		if (is_array($arrObjData)) {
			foreach ($arrObjData as $index => $value) {
				if (is_object($value) || is_array($value)) {
					$value = xmltoarray($value, $arrSkipIndices); // recursive call
				}
				if (in_array($index, $arrSkipIndices)) {
					continue;
				}
				$arrData[strtolower($index)] = $value;
			}
		}
		return $arrData;
	}
?>