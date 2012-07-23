<?php
	get_function(array('dev','class_de','race_de','getlvbycolor','itemlink','class_color'));
	
	$mysqli = new mysqli(DB_HOST, DB_USER,DB_PW, DB_DB);
	if (mysqli_connect_errno()){
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	$mysqli->set_charset("utf8");
	$sql = 'SELECT * FROM raids WHERE raid_id = '.urldecode($pathinfo[1]).';';
	$result_raiddata = $mysqli->query($sql);
	$raiddata = $result_raiddata->fetch_assoc();
	unset($sql);
	$date = date("d.m.Y",strtotime($raiddata['raid_date']));
	
	$sql = "SELECT characters.char_id, characters.char_name, characters.char_guild, characters.char_class FROM  characters, raidplayerlist WHERE characters.char_id = raidplayerlist.char_id AND raid_id = '".((int)($pathinfo[1]))."' ORDER BY characters.char_name ASC";
	$result_raidchardata = $mysqli->query($sql);
	while($raidchardata = $result_raidchardata->fetch_assoc()){
		$raidchars[] = $raidchardata;
	};
	unset($sql);
	unset($result_raidchardata);
	
	$sql = "SELECT raid_bosses.boss_name, raidbosskills.difficulty FROM raidbosskills, raid_bosses WHERE raidbosskills.boss_id = raid_bosses.boss_id  AND raidbosskills.raid_id='".((int)($pathinfo[1]))."'";
	$result = $mysqli->query($sql);
	while($boss = $result->fetch_assoc()){
		$bosse[] = $boss;
	}
	unset($sql);
	unset($result);
	
	$sql = "SELECT items.entry_id, items.item_name, items.item_id, items.item_icon, items.quality, items.player, characters.char_name, characters.char_class, items.costs FROM items, characters WHERE items.player = characters.char_id  AND items.raid_id='".((int)($pathinfo[1]))."'";
	$result_itemdata = $mysqli->query($sql);
	while($items = $result_itemdata->fetch_assoc()){
		$itemdata[] = $items;
	};
	unset($sql);
	unset($result_itemdata);
	
	$chars = Array(
		'Druide' => Array(),
		'Hexenmeister' => Array(),
		'Jäger' => Array(),
		'Krieger' => Array(),
		'Magier' => Array(),
		'Paladin' => Array(),
		'Priester' => Array(),
		'Schamane' => Array(),
		'Schurke' => Array(),
		'Todesritter' => Array()
	);
	foreach($raidchars as $rowchars){
		array_push($chars[class_de($rowchars['char_class'])], $rowchars);
	};
	
	$mysqli->close();
?>
<table style="text-align: left; margin: 5px 0;">
	<tr>
		<td style="width: 100px;">Instanz :</td><td><?php echo $raiddata['raid_name'];?></td>
	</tr>
	<tr>
		<td>Beschreibung :</td><td><?php echo $raiddata['raid_description'];?></td>
	</tr>
	<tr>
		<td>Datum :</td><td><?php echo $date; ?></td>
	</tr>
	<tr>
		<td>DKP :</td><td><span class="positive"><?php echo $raiddata['dkp'];?></span></td>
	</tr>
</table>
<div style="width: 960px; display: block; text-align: left; margin: 5px 0; border: 1px solid grey; height: 100%,">
	<?php foreach($raidchars as $char){?>
	<a href="/character/<?php echo $char['char_name'];?>" style="display: inline-block; width: 185px;">
		<span class="<?php echo $char['char_class'];?> bold"><?php echo $char['char_name'];?></span>
	</a>
	<?php };?>
</div>
<table style="text-align: left; border: 1px solid red;max-height: 1200px; min-height: 700px;height: 300px; line-height: 100%;width: 400px;clear: both;">
	<tr style="height: 11px;">
		<th>K&auml;ufer</th><th>Item</th><th>Kosten</th>
	</tr>
	<?php foreach($itemdata as $item){?>
	<tr>
		<td>
			<a href="/character/<?php echo $item['char_name'];?>" style="display: inline-block;">
				<span class="<?php echo $item['char_class'];?> bold"><?php echo $item['char_name'];?></span>
			</a>
		</td>
		<td>
			<?php echo ItemLink($item['item_id'], $item['item_icon'], $item['quality'],  $item['item_name']);?>
		</td>
		<td class="dkplisting negative"><?php echo $item['costs']?></td>
	</tr>
	<?php };?>
</table>