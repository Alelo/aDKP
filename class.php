<table style='text-align:left;' id="classlisting">
	<tr id="headings">
	<?php
		$th = array(
			array('name','Name'),
			array('talents','Skillung'),
			array('gain','Bekommen'),
			array('spent','Ausgegeben'),
			array('individual','Individual'),
			array('all','Gesamt'),
			array('attendance14','RA 14', 'Raid Attendance : 14 days'),
			array('attendance30','RA 30', 'Raid Attendance : 30 days'),
			array('attendance','RA All', 'Raid Attendance : full')
		);
		foreach($th as $the){
			echo '<th><a href="/'.$pathinfo[0].'/'.$pathinfo[1].'/'.$the[0].'/';
			if($pathinfo[2] == $the[0]){
				echo ($pathinfo[3]=="asc" || $pathinfo[3]==null)? 'desc': 'asc';	
			} else {
				echo 'asc';
			}
			echo '" ';
			if($the[2]){
				echo 'title="'.$the[2].'"';
			}
			echo '>'.$the[1].'</a></th>';
		}
	?></tr><?php
	get_function(array('dev','class_de','race_de','getlvbycolor'));
	$chardata[] = array();
	
	$mysqli = new mysqli(DB_HOST, DB_USER,DB_PW, DB_DB);
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	$mysqli->set_charset("utf8");
	$sqlchardata = "SELECT char_name FROM characters WHERE char_class = '".strtoupper($pathinfo[1])."' ORDER BY char_name ASC";
	$resultchardata = $mysqli->query($sqlchardata);
	while($x = $resultchardata->fetch_assoc()){
		$chardata[$x['char_name']] = array();
	}
	$sql = "SELECT * FROM  `characters`, `redundant_dkp` WHERE characters.char_id = redundant_dkp.char_id AND `char_class` LIKE  '".strtoupper($pathinfo[1])."'";
	$resultchars = $mysqli->query($sql);
	
	$sqldkp = "SELECT * FROM `redundant_dkp`";
	$resultdkp = $mysqli->query($sqldkp);
	while($rowdkp = $resultdkp->fetch_assoc()){
		$dkp[$rowdkp['char_id']] = $rowdkp['dkp'];
	};
	
	$raids14 = array();
	$sql = "SELECT `raid_id` FROM `raids` WHERE `raid_date` >= date_sub(DATE_FORMAT(curdate(), '%m/%d/%y'),interval 14 day)";
	$result = $mysqli->query($sql);
	while ($raid = $result->fetch_assoc()){
		array_push($raids14, $raid['raid_id']);
	}
	$raids30 = array();
	$sql = "SELECT `raid_id` FROM `raids` WHERE `raid_date` >= date_sub(DATE_FORMAT(curdate(), '%m/%d/%y'),interval 1 month)";
	$result = $mysqli->query($sql);
	while ($raid = $result->fetch_assoc()){
		array_push($raids30, $raid['raid_id']);
	}
	
	
	while ($row = $resultchars->fetch_assoc()) { 
		
		$sql = 'SELECT `raid_id` FROM `raids`';
		$result = $mysqli->query($sql);
		$row['raidcount'] = mysqli_num_rows($result);
		
		$sql = 'SELECT `raid_id` FROM `raids` WHERE `raid_date` <= date_sub(curdate(),interval 14 day)';
		$result = $mysqli->query($sql);
		$row['raidcount14'] = mysqli_num_rows($result);
		
		$sql = 'SELECT `raid_id` FROM `raids` WHERE `raid_date` <= date_sub(curdate(),interval 30 day)';
		$result = $mysqli->query($sql);
		$row['raidcount30'] = mysqli_num_rows($result);
		
		
		$sqlatendance = 'SELECT * FROM `raidplayerlist` WHERE `char_id`='.$row['char_id'].' ORDER BY `raid_id` DESC';
		$resultattendance = $mysqli->query($sqlatendance);
		$attedance = $resultattendance->fetch_assoc();
		
		$row['raiddependens'] = Array();
		$sql = 'SELECT * FROM `raidplayerlist` WHERE `char_id`='.$row['char_id'].' ORDER BY `raid_id` DESC';
		$result = $mysqli->query($sql);
		while( $data = $result->fetch_assoc())
		{
			array_push($row['raiddependens'], $data);
		}
		$row['raiddependensall'] = count($row['raiddependens']);
		if($result->field_count != 0 ){
			// Initiere und beziehe Raids
			$row['raids'] = Array();
			foreach( $row['raiddependens'] as $deps ){
				$sql = 'SELECT * FROM `raids` WHERE `raid_id`='.$deps['raid_id'].' ORDER BY `raid_id` DESC';
				$result = $mysqli->query($sql);
				array_push($row['raids'], $result->fetch_assoc());
				
				
				$row['raids14'] = array();
				$sql = "SELECT raids.raid_id FROM raids, raidplayerlist WHERE raids.raid_id = raidplayerlist.raid_id  AND raid_date >= date_sub(DATE_FORMAT(curdate(), '%m/%d/%y'),interval 14 day) AND raidplayerlist.char_id=".$row['char_id']." AND raids.raid_id=".$deps['raid_id'];
				$result = $mysqli->query($sql);
				while($raid = $result->fetch_assoc()){
					array_push($row['raids14'], $raid['raid_id']);
				}
				
				
				$row['raids30'] = array();
				$sql = "SELECT raids.raid_id FROM raids, raidplayerlist WHERE raids.raid_id = raidplayerlist.raid_id  AND raid_date >= date_sub(DATE_FORMAT(curdate(), '%m/%d/%y'),interval 30 day) AND raidplayerlist.char_id=".$row['char_id']." AND raids.raid_id=".$deps['raid_id'];
				$result = $mysqli->query($sql);
				while($raid = $result->fetch_assoc()){
					array_push($row['raids30'], $raid['raid_id']);
				}
			}
		}

		
		$row['items'] = Array();
		$sql = 'SELECT * FROM `items` WHERE `player`='.$row['char_id'].' ORDER BY `time` DESC';	
		$result = $mysqli->query($sql);
		while($data = $result->fetch_assoc()){
			array_push($row['items'], $data);
		}
		
		$row['individual'] = Array();
		$sql = 'SELECT * FROM `individual_dkp` WHERE `char_id`='.$row['char_id'].' ORDER BY `entry_id` DESC';
		$result = $mysqli->query($sql);
		while( $data = $result->fetch_assoc() ){
			array_push($row['individual'], $data);
		}
		$gain = 0;
		$spend = 0;
		$correction = 0;
		if( is_array($row['raids'])){
			foreach($row['raids'] as $raid )
				$gain += $raid['dkp'];
		}
		$row['gain'] = $gain;
		foreach($row['items'] as $item )
			$spend += $item['costs'];
		$row['spend'] = $spend;
		foreach( $row['individual'] as $entry )
			$correction += $entry['dkp'];
		$row['correction'] = $correction;
		$sum = $gain - $spend + $correction;
		$row['sum'] = $sum;
		
		
		$chardata[$row['char_name']] =  $row;
	}
	$mysqli->close();
	if(isset($chardata[0])){
		unset($chardata[0]);
	}
?>
<?php
	switch ($pathinfo['2']) {
		case 'name':
			$sorter = 'char_name';
			break;
		case 'gain':
			$sorter = 'gain';
			break;
		case 'spent':
			$sorter = 'spend';
			break;
		case 'individual':
			$sorter = 'correction';
			break;
		case 'all':
			$sorter = 'dkp';
			break;
		case 'attendance':
			$sorter = 'raiddependensall';
			break;
		case 'attendance14':
			$sorter = 'raids14';
			break;
		case 'attendance30':
			$sorter = 'raids30';
			break;
		default:
			$sorter = 'char_name';
	}
	switch ($pathinfo['3']){
		case 'asc':
			$way = 1;
			break;
		case 'desc':
			$way = -1;
			break;
		default:
			$way = 1;
	}

	function fieldSortComp($a, $b) {
		global $sortCriteria;
			foreach($sortCriteria as $fieldName => $orderDir) {
				if ($a[$fieldName]!=$b[$fieldName]) {
					return ($a[$fieldName]>$b[$fieldName]) ? $orderDir : 0 - $orderDir;
				}
			}
		return 0;
	}
	if($pathinfo[2]!='name'){
		$sortCriteria = array('char_name' => -1);
		usort($chardata, 'fieldSortComp');
	}
	$sortCriteria = array($sorter => $way);
	usort($chardata, 'fieldSortComp');

?>
<?php foreach($chardata as $char){ ?>
	<tr>
		<td>
			<a href="/character/<?php echo $char['char_name'];?>" class="player">
				<span class="<?php echo $char['char_class'];?>">
					<?php echo $char['char_name'];?>
				</span>
			</a>
		</td>
		<td>
			<span>
				<abbr title="(noch ned implementiert da Char verwaltung(beta2/3)nicht existiert)">Skillung</abbr>
			</span>
		</td>
		<td>
			<span class="positive">
				<?php echo $char['gain']; ?>
			</span>
		</td>
		<td>
			<span class="negative">
				<?php echo $char['spend']; ?>
			</span>
		</td>
		<td>
			<span class="<?php echo ($correction >= 0)? 'positive': 'negative'; ?>">
				<?php echo $char['correction']; ?>
			</span>
		</td>
		<td>
			<span class="<?php echo ($char['sum'] >= 0)? 'positive': 'negative'; ?>">
				<?php echo $char['sum']; ?>
			</span>
		</td>
		<?php /*HIER unteren teil der Attendence1430.php einfügen*/?>
		<td style="text-align: right;">
				<b><?php echo ((int)(count($char['raids14']) / $char['raidcount14'] * 100))?>%</b>
		</td>
		<td style="text-align: right;">
				<b><?php echo ((int)(count($char['raids30']) / $char['raidcount30'] * 100))?>%</b>
		</td>
		<td style="text-align: right;">
				<b><?php echo ((int)(count($char['raids']) /  $char['raidcount'] * 100))?>%</b>
		</td>
	</tr>
<?php } ?>
</table>