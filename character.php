<!-- statistic icon source: http://www.famfamfam.com/lab/icons/silk/ -->
<?php
// Notiz-Todo: Tabellarische Auflistung mit <th> versehen!
	get_function(array('dev','class_de','race_de','getlvbycolor','itemlink'));
	
	$mysqli = new mysqli(DB_HOST, DB_USER,DB_PW, DB_DB);
	if (mysqli_connect_errno()) {
    	printf("Connect failed: %s\n", mysqli_connect_error());
    	exit();
	}
	
	// Selektiere Character aus Datenbank
	$sql = 'SELECT * FROM characters WHERE char_name="'.urldecode($pathinfo[1]).'"';
	$mysqli->set_charset("utf8");
	$resultchars = $mysqli->query($sql);
	$plr = $resultchars->fetch_assoc();
	// Pruefen ob Daten zurueckgegeben wurden
	if(is_array($plr)){
		// Character gefunden, beziehe weitere Daten
		// Zaehle gesamte anzahl an raids
		$sql = 'SELECT raid_id FROM raids';
		$result = $mysqli->query($sql);
		$plr['raidcount'] = mysqli_num_rows($result);

		// Initiere und beziehe Raid-Zugehoerigkeiten
		$plr['raiddepends'] = Array();
		$sql = 'SELECT * FROM raidplayerlist WHERE char_id='.$plr['char_id'].' ORDER BY raid_id DESC';	
		$result = $mysqli->query($sql);
		while( $data = $result->fetch_assoc()){
			array_push($plr['raiddepends'], $data);
		}
		// Pruefen ob Daten zurueckgegeben wurden. Sind keine Daten eingetragen worden
		// In diesem Fall waere es verschwendete Rechenleistung, wenn man weiter sucht :P
		if($result->field_count != 0 ){
			// Initiere und beziehe Raids
			$plr['raids'] = Array();
			foreach( $plr['raiddepends'] as $deps ){
				$sql = 'SELECT * FROM raids WHERE raid_id='.$deps['raid_id'].' ORDER BY raid_id DESC';
				$result = $mysqli->query($sql);
				array_push($plr['raids'], $result->fetch_assoc());
			}
		}
		// Initiere und Beziehe Items
		$plr['items'] = Array();
		$sql = 'SELECT * FROM items WHERE player='.$plr['char_id'].' ORDER BY time DESC';	
		$result = $mysqli->query($sql);
		while( $data = $result->fetch_assoc() ){
			array_push($plr['items'], $data);
		}
		// Initiere und Beziehe individuelle Korrekturen
		$plr['individual'] = Array();
		$sql = 'SELECT * FROM individual_dkp WHERE char_id='.$plr['char_id'].' ORDER BY entry_id DESC';
		$result = $mysqli->query($sql);
		while( $data = $result->fetch_assoc() ){
			array_push($plr['individual'], $data);
		}
		$mysqli->close();
		// Aufbauen der Datentabellen
		?>
		<table style="text-align: left;">
			<tr>
				<td width="800" align="center" colspan="2" style="padding-bottom: 15px;">
				<?php					
					$str = $plr['char_name'] .' - ';
					if($plr['char_sex'] == '3'){
						$str .= " weiblicher ";
					}elseif($plr['char_sex'] == '2'){
						$str .= " m&auml;nnlicher ";
					};
					$str = $str .''. race_de($plr['char_race']) .' ';
					$str = $str .''. class_de($plr['char_class']) .' | ';
					$guild = ($plr['char_guild'] == '') ? 'Keine Gilde' : '&lt;'.$plr['char_guild'].'&gt;';
					$str = $str .''. $guild;
					echo $str;
				 ?>
				</td>
			</tr>
			<tr>
				<td width="400" valign="top">
					<?php
						$gain = 0;
						$spend = 0;
						$correction = 0;
						if( gettype($plr['raids']) == "array" ){
							foreach( $plr['raids'] as $raid )
								$gain += $raid['dkp'];
						}	
						foreach( $plr['items'] as $item )
							$spend += $item['costs'];
							
						foreach( $plr['individual'] as $entry )
							$correction += $entry['dkp'];
						?>
						<span class="dkpdescription">Bekommen:</span> <span style="color:#33cc33" class="dkpdescriptionvalues"><?php echo $gain ?></span><br />
						<span class="dkpdescription">Ausgegeben:</span> <span style="color:#E23B30" class="dkpdescriptionvalues"><?php echo $spend ?></span><br />
						<span class="dkpdescription">Korrektur:</span> <span style="color:#<?php echo(($correction >= 0)? '33cc33' : 'E23B30')?>" class="dkpdescriptionvalues"><?php echo $correction ?></span><br />
						<?php $sum = $gain - $spend + $correction;?>
						<span class="dkpdescription">Jetzt:</span> <span style="color:#<?php echo(($sum >= 0)? '33cc33' : 'E23B30')?>" class="dkpdescriptionvalues"><?php echo $sum ?></span><br />
				</td>
				<td width="400" valign="top">
					<?php
						$raids = $plr['raidcount'];
						$attended = count($plr['raids']);
						?>Raid Attendance:
						<div class="graph">
						<strong class="bar" style="width: <?php echo ((int)($attended / $raids * 100))?>%;"><?php echo $attended.' ('.((int)($attended / $raids * 100))?>%)</strong>
						</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top">
					<table width="100%" class="dkp">
						<tr class="bold">
							<td colspan="4">Raids</td>
						</tr>
						<tr class="bold">
							<td>Datum</td>
							<td>Name</td>
							<td>Beschreibung</td>
							<td width="50" style="padding-left: 10px;">DKP</td>
						</tr>
						<?php if(is_array($plr['raids'])){
							foreach( $plr['raids'] as $raid ){
							$explodeddate = explode(' ', $raid['raid_date']); ?><tr>
								<td width="70"><?php echo $explodeddate[0]?></td>
								<td width="180"><?php echo $raid['raid_name']?></td>
								<td><?php echo $raid['raid_description']?></td>
								<td class="dkplisting"><?php echo $raid['dkp']?></td>
							</tr><?php 
							}
						}?>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top">
					<table width="100%" class="dkp">
						<tr class="bold">
							<td colspan="3">Items</td>
						</tr>
						<tr class="bold">
							<td width="70">Datum</td>
							<td width="520">Item</td>
							<td width="40" style="padding-left: 10px;">DKP</td>
						</tr>
						<?php foreach( $plr['items'] as $item ){ ?>
						<tr>
							<td width="70"><?php $explodedtime = explode(' ',$item['time']); echo $explodedtime[0]?></td>
							<td><?php echo ItemLink($item['item_id'], $item['item_icon'], $item['quality'],  $item['item_name']);?></td>
							<td class="dkplisting"><?php echo $item['costs']?></td>
						</tr>
						<?php }?>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top">
					<table width="100%" class="dkp">
						<tr class="bold">
							<td colspan="3">Korrekturen</td>
						</tr>
						<tr class="bold">
							<td width="70">Datum</td>
							<td width="520">Grund</td>
							<td width="40" style="padding-left: 10px;">DKP</td>
						</tr>
						<?php foreach( $plr['individual'] as $entry ){ ?>
						<tr>
							<td width="70"><?php $explodeddate = explode(' ',$entry['date']); echo $explodeddate[0]?></td>
							<td><?php echo htmlentities($entry['occasion'])?></td>
							<td class="dkplisting"><?php echo $entry['dkp']?></td>
						</tr>
						<?php } ?>
					</table>
				</td>
			</tr>
		</table>
		<?php
		//fprint_r($plr);
	} else {
		// Character nicht gefunden, abbruch
		?>Invalid name<?php 
		$mysqli->close;
	}	
?>