<?php
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// Beziehen der Daten für DKP Neuberechnung
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		require_once('config.php');	
		$mysqli = new mysqli(DB_HOST, DB_USER,DB_PW, DB_DB);
		if (mysqli_connect_errno()) {
	    	printf("Connect failed: %s\n", mysqli_connect_error());
	    	exit();
		};
		echo 'Beginning DKP recalculation<br>';
		// Raids beziehen
		$sql = 'SELECT `raid_id`,`dkp` FROM `raids`';
		$result = $mysqli->query($sql);
		$raids = Array();
		while($row = $result->fetch_assoc())
		{
			array_push($raids, $row);
		}
		
		// Individual DKP bzeiehen
		$sql = 'SELECT `char_id`,`dkp` FROM `individual_dkp`';
		$result = $mysqli->query($sql);
		$individuallist = Array();
		while($row = $result->fetch_assoc())
		{
			array_push($individuallist, $row);
		}
		
		// Loot beziehen
		$sql = 'SELECT `player`,`costs` FROM `items`';
		$result = $mysqli->query($sql);
		$items = Array();
		while($row = $result->fetch_assoc())
		{
			array_push($items, $row);
		}
		
		// Charaktere Beziehen
		$sql = 'SELECT `char_id`,`char_name` FROM `characters`';
		$result = $mysqli->query($sql);
		$chars = Array();
		while($row = $result->fetch_assoc())
		{
			array_push($chars, $row);
		}
		
		// RaidPlayerliste Beziehen
		$sql = 'SELECT `char_id`,`raid_id` FROM `raidplayerlist`';
		$result = $mysqli->query($sql);
		$plrlist = Array();
		while($row = $result->fetch_assoc())
		{
			array_push($plrlist, $row);
		}
		
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// Beginne DKP neuberechnung
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		foreach( $chars as $plr )
		{
			$points = 0;
			// Raids:
			foreach( $raids as $raid )
			{
				foreach( $plrlist as $plrFromList )
				{
					// Wenn der Aktuelle Eintrag zum Aktuellen Raid gehört und der aktuelle Spieler dabei war, gebe DKP
					if( $plrFromList['raid_id'] == $raid['raid_id'] and $plr['char_id'] == $plrFromList['char_id'] )
					{
						$points += $raid['dkp'];
					}
				}
			}
			
			// Individual DKP
			foreach( $individuallist as $entry )
			{
				if( $entry['char_id'] == $plr['char_id'] )
				{
					$points += $entry['dkp'];
				}
			}
			
			// Items
			foreach( $items as $item )
			{
				if( $item['player'] == $plr['char_id'] )
				{
					$points -= $item['costs'];
				}
			}
			$sql = 'INSERT INTO `redundant_dkp` (`char_id`,`dkp`) VALUES ('.$plr['char_id'].','.$points.') ON DUPLICATE KEY UPDATE `dkp`='.$points;
			$mysqli->query($sql);
		}
		
		echo 'Recalculation done.';
		$mysqli->close();
	?>