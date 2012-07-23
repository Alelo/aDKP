<pre style="text-align: left;">
<?php
	get_function(array('dev','class_de','race_de','getlvbycolor'));
	$mysqli = new mysqli(DB_HOST, DB_USER,DB_PW, DB_DB);
	if (mysqli_connect_errno()) {
    	printf("Connect failed: %s\n", mysqli_connect_error());
    	exit();
	}
	$mysqli->set_charset("utf8");
	// Schoen einfach machen :) Item-Name oder ID *g*
	$itemFromGet = mysqli_real_escape_string($mysqli, $pathinfo[1]);
	if(!$itemFromGet)
	{
		die('Fehler: Keinen Gegenstand &uuml;bergeben.'); // Sollte noch angepasst werden
	}
	// Folgende Query waehlt alle Items mithilfe der Info der Get-Variablen und joint dazu die entsprechenden Spieler-Daten
	$result_items = $mysqli->query('SELECT items.item_name, items.item_id, items.item_icon, items.quality, items.costs, items.time, characters.char_id, characters.char_name, characters.char_class FROM items, characters
									WHERE items.Player = characters.char_id
									AND item_id = "'.$itemFromGet.'" ORDER BY items.time DESC');
	$itemlist = Array();
	while( $row = $result_items->fetch_assoc() )
	{
		array_push($itemlist, $row);
	}
	//fprint_r($itemlist);
	$mysqli->close();
#	print_r($itemlist);
?></pre>
					<table class="dkp">
						<tr>
							<td align="center" colspan="4" class="bold">Drop Historie von <a href="http://www.wowhead.com/?item=<?php echo $itemlist[0]['item_id'];?>" class="<?php echo getLvByColor($itemlist[0]['quality']);?>" style="text-decoration: none;">[<?php echo $itemlist[0]['item_name']; ?>]</a></td>
						</tr>
						<tr>
							<td align="center" width="130" class="bold">Datum</td>
							<td width="150" class="bold">Looter</td>
							<td align="center" class="bold" width="40">DKP</td>
						</tr>
						<?php foreach($itemlist as $item){ ;?>
							<tr>
								<td align="center"><?php echo explode(' ',$item['time']);?></td>
								<td align="center" class="<?php echo $item['char_class']?> bold" style="text-align: left;"><a style="text-decoration: none;" class="<?php echo $item['char_class']?>" href="/character/<?php echo $item['char_name'];?>"><?php echo $item['char_name'];?></a></td>
								<td align="center" class="dkplisting"><?php echo $item['costs'];?></td>
							</tr>
						<?php } ?>
					</table>