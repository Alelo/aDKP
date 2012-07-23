<?php
	function statusmes($message){
		echo '<pre><b><font color="orange">Status:</font></b> '.$message.'</pre>';
	}
	
	function getCharIdByName($charname){
		$sql = 'SELECT `char_id` FROM `characters` WHERE `char_name`="'.$charname.'"';
		$result = mysql_query($sql);
		$data = mysql_fetch_assoc($result);
		return $data['char_id'];
	}
	
	function getBossIdByName($bossname){
		if($bossname == 'Prince Keleseth' || $bossname == 'Prince Taldaram' || $bossname == 'Prince Valanar'){
			$bossname = 'Blood Prince Council';
		}
		$sql = 'SELECT `boss_id` FROM `raid_bosses` WHERE `boss_name`="'.$bossname.'"';
		$result = mysql_query($sql);
		$data = mysql_fetch_assoc($result);
		if(empty($data)){
			$data['boss_id'] = '0';
		}
		return $data['boss_id'];
	}
	
	$start = explode(' ', microtime());
	require_once('func/xmltoarray.func.php');
	$xmlObj = simplexml_load_string($_POST['file']);
	$cvt_xml = xmltoarray($xmlObj);

	
	if(isset($_POST['add'])){	
		$data['name'] = mysql_escape_string($_POST['raid_name']);
		$data['desc'] = mysql_escape_string($_POST['raid_desc']);
		$data['date'] = mysql_escape_string($_POST['date']);
		$data['dkp'] = mysql_escape_string($_POST['dkp']);
		
		include('config.php');
		$sock = mysql_connect(DB_HOST, DB_USER,DB_PW);
		mysql_select_db(DB_DB);
		mysql_set_charset('utf8', $sock);
		
		# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ #
		# Charaktere in die Datenbank eintragen, falls noch nicht vorhanden   #
		# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ #
		statusmes('compare chars with database');
		foreach( $cvt_xml['playerinfos'] as $plr ){
			$result = mysql_query('SELECT `char_id` FROM `characters` WHERE char_name="'.$plr['name'].'"');
			if(mysql_num_rows($result) == 1)
				continue;
			$sql = 'INSERT INTO `characters` (`char_name`,`char_guild`,`char_class`,`char_race`,`char_sex`) VALUES ("'.$plr['name'].'","'.$plr['guild'].'","'.$plr['class'].'","'.$plr['race'].'",'.$plr['sex'].')';
			mysql_query($sql);
		}
		
		# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		# Instanz in die Datenbank eintragen, falls noch nicht vorhanden
		# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		statusmes('compare instance with database');
		$result = mysql_query('SELECT `instance_name` FROM `raid_instances` WHERE instance_name="'.$cvt_xml['instance']['name'].'"');
		if(mysql_num_rows($result) == 1)
			continue;
		$sql = 'INSERT INTO `raid_instances` (`instance_name`) VALUES ("'.$cvt_xml['instance']['name'].'")';
		mysql_query($sql);
		$instance_id = mysql_insert_id();
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// Instanz Bosse  in die Datenbank eintragen, falls noch nicht vorhanden
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		
		$bosses = array();
		foreach($cvt_xml['bosskills'] as $boss ){
			if($boss['name'] == 'Prince Keleseth' || $boss['name'] == 'Prince Taldaram' || $boss['name'] == 'Prince Valanar'){
				$boss['name'] = 'Blood Prince Council';
			}
			array_push($bosses, $boss);
		}
		
		function super_unique($array){
			$result = array_map("unserialize", array_unique(array_map("serialize", $array)));
			foreach ($result as $key => $value){
				if ( is_array($value) ){
					$result[$key] = super_unique($value);
				}
			}
			return $result;
		}
		$bosses = super_unique($bosses);
		
		statusmes('compare instance bosses with database');
		foreach( $bosses as $boss ){
			$result = mysql_query('SELECT `boss_name` FROM `raid_bosses` WHERE boss_name="'.$boss['name'].'"');
			if(mysql_num_rows($result) == 1)
				continue;
			$sql = 'INSERT INTO `raid_bosses` (`instance_id`,`boss_name`) VALUES ("'.$instance_id.'","'.$boss['name'].'")';
			mysql_query($sql);
		}
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++		
		// Eintragen des Raids in die Datenbank
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		statusmes('insert raid into database');
		$sql = 'INSERT INTO `raids` (`raid_name`,`raid_description`,`raid_date`,`dkp`) VALUES ("'.$data['name'].'","'.$data['desc'].'","'.$data['date'].'",'.$data['dkp'].')';
		mysql_query($sql);
		// Abrufen der Raid ID
		$raid_id = mysql_insert_id();
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// Playerlist eintragen
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		statusmes('Generating RaidPlayerlist');
		foreach( $cvt_xml['playerinfos'] as $plr ){
			$sql = 'INSERT INTO `raidplayerlist` (`char_id`,`raid_id`) VALUES ('.getCharIdByName($plr['name']).','.$raid_id.')';
			mysql_query($sql);
		}
		
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// Raid Boss Kill list eintragen
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		statusmes('Generating Raid_Boss_kill_list');
		if($cvt_xml['bosskills'] !== NULL){
			foreach( $bosses as $kill ){
				$sql = 'INSERT INTO `raidbosskills` (`raid_id`,`boss_id`,`kill_time`, `difficulty`) VALUES';
				$sql .= ' ("'.$raid_id.'","'.getBossIdByName($kill['name']).'","'.$kill['time'].'","'.$kill['instance']['difficulty'].'")';
				mysql_query($sql);
			}
		}
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// Eintragen der Items
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		statusmes('adding loot to database');
		foreach( $cvt_xml['loot'] as $item ){
			$tmp = explode(':',$item['itemid']);
			$item['itemid'] = $tmp[0];
			$sql = 'INSERT INTO `items` (`raid_id`,`item_name`,`item_id`,`item_icon`,`boss_id`,`class`,`subclass`,`quality`,`count`,`player`,`costs`,`time`) VALUES ';
			$sql .= '('.$raid_id.',"'.$item['itemname'].'",'.$item['itemid'].',"'.strtolower($item['icon']).'","'.getBossIdByName($item['boss']).'","'.$item['class'].'","'.$item['subclass'].'",';
			$sql .= '"'.substr($item['color'],2).'",'.$item['count'].','.getCharIdByName($item['player']).','.$item['costs'].',"'.$item['time'].'")';
			mysql_query($sql);
		}
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// Cvt_XML Variabel löschen um Speicher frei zu räumen
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		statusmes('Delete Variable Cache');
		unset($cvt_xml);
		unset($item);
		unset($raid_id);
		unset($sql);
		unset($plr);
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// Beziehen der Daten für DKP Neuberechnung
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		statusmes('Beginning DKP recalculation');
		// Raids beziehen
		$sql = 'SELECT `raid_id`,`dkp` FROM `raids`';
		$result = mysql_query($sql);
		$raids = Array();
		while($row = mysql_fetch_assoc($result)){
			array_push($raids, $row);
		}
		
		// Individual DKP bzeiehen
		$sql = 'SELECT `char_id`,`dkp` FROM `individual_dkp`';
		$result = mysql_query($sql);
		$individuallist = Array();
		while($row = mysql_fetch_assoc($result)){
			array_push($individuallist, $row);
		}
		
		// Loot beziehen
		$sql = 'SELECT `player`,`costs` FROM `items`';
		$result = mysql_query($sql);
		$items = Array();
		while($row = mysql_fetch_assoc($result)){
			array_push($items, $row);
		}
		
		// Charaktere Beziehen
		$sql = 'SELECT `char_id`,`char_name` FROM `characters`';
		$result = mysql_query($sql);
		$chars = Array();
		while($row = mysql_fetch_assoc($result)){
			array_push($chars, $row);
		}
		
		// RaidPlayerliste Beziehen
		$sql = 'SELECT `char_id`,`raid_id` FROM `raidplayerlist`';
		$result = mysql_query($sql);
		$plrlist = Array();
		while($row = mysql_fetch_assoc($result)){
			array_push($plrlist, $row);
		}
		
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// Beginne DKP neuberechnung
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		foreach( $chars as $plr ){
			$points = 0;
			// Raids:
			foreach( $raids as $raid ){
				foreach( $plrlist as $plrFromList ){
					// Wenn der Aktuelle Eintrag zum Aktuellen Raid gehört und der aktuelle Spieler dabei war, gebe DKP
					if( $plrFromList['raid_id'] == $raid['raid_id'] and $plr['char_id'] == $plrFromList['char_id'] ){
						$points += $raid['dkp'];
					}
				}
			}
			
			// Individual DKP
			foreach( $individuallist as $entry ){
				if( $entry['char_id'] == $plr['char_id'] ){
					$points += $entry['dkp'];
				}
			}
			
			// Items
			foreach( $items as $item ){
				if( $item['player'] == $plr['char_id'] ){
					$points -= $item['costs'];
				}
			}
			$sql = 'INSERT INTO `redundant_dkp` (`char_id`,`dkp`) VALUES ('.$plr['char_id'].','.$points.') ON DUPLICATE KEY UPDATE `dkp`='.$points;
			mysql_query($sql);
		}
		
		statusmes('Recalculation done.');
		mysql_close($sock);
		
	} else {
		?><form action="addtodb.php" method="post"><table align="center" accept-charset="UTF-8">
		<tr><td align="right">Raid:</td><td><input name="raid_name" type="text" value="<?php echo $cvt_xml['zone']?>"></td></tr>
		<tr><td align="right">Beschreibung:</td><td><input name="raid_desc" type="text"></td></tr>
		<tr><td align="right">Datum:</td><td><input name="date" type="text" value="<?php echo $cvt_xml['key']?>"></td></tr>
		<tr><td align="right">DKP:</td><td><input name="dkp" type="text"></td></tr>
		<tr><td><input type="hidden" name="file" value="<?php echo $_POST['file']?>"></td></tr>
		<tr><td align="center" colspan="2"><input type="submit" name="add" value="Eintragen">
		</table></form>
	<?php }
	
	// Zeit Anzeigen:
	$end = explode(' ', microtime());
	$rend = $end[0] + $end[1];
	$rstart = $start[0] + $start[1];
	echo '<div align="center">'.round($rend - $rstart, 3) .' sec</div>';
?>
