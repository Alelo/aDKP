<?php
	/*
	 * Usage: listraids($count);
	 * @return: html table with raidlist
	 */

	function listraids($count = 10){
		$mysqli = new mysqli(DB_HOST, DB_USER,DB_PW, DB_DB);
		if(mysqli_connect_errno()){
	    	printf("Connect failed: %s\n", mysqli_connect_error());
	    	exit();
		};
		$mysqli->set_charset("utf8");
		$sql = 'SELECT * FROM raids ORDER BY raid_id DESC'.(($count <= 0)? ';':' LIMIT '.$count.';');
		$result = $mysqli->query($sql);
		$raids = Array();
		while($data = $result->fetch_assoc()){
			array_push($raids, $data);
		}
		$mysqli->close();
		$return = '
		<table class="dkp center">
			<tr>
				<td align="center" colspan="4" class="bold"><span>Vergangene Raids</span></td>
			</tr>
			<tr>
				<td align="center" width="130" class="bold"><span>Datum</span></td>
				<td align="center" width="210" class="bold"><span>Name</span></td>
				<td align="center" width="350" class="bold"><span>Beschreibung</span></td>
				<td align="center" width="40" class="bold"><span>DKP</span></td>
			</tr>';
		foreach($raids as $raid){
		$exploderaiddate = explode(' ',$raid['raid_date']);
		$return .= '
			<tr class="q5">
				<td align="center"><a href="raidhistory/'.$raid['raid_id'].'">'.$exploderaiddate[0].'</a></td>
				<td align="center"><a href="raidhistory/'.$raid['raid_id'].'"><span>'.$raid['raid_name'].'</span></a></td>
				<td style="text-align: left;"><a href="raidhistory/'.$raid['raid_id'].'"><span>'.$raid['raid_description'].'</span></a></td>
				<td align="center"><span class="positive">'.$raid['dkp'].'</span></td>
			</tr>';
		}
		$return .='</table>';
		return $return;
	}
?>