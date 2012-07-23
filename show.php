<?php
	$xmlfile = $_GET['f'];
	echo $_GET['f'];
	require_once('interpret.php');
	echo 'Invite: '.$cvt_xml['date'] .'<br>';
	echo 'Realm: '.$cvt_xml['realm'] .'<br>';
	echo 'Start: '.$cvt_xml['start'] .'<br>';
	echo 'End: '.$cvt_xml['end'] .'<br>';
	echo 'Zone: '.$cvt_xml['zone'] .'<br>';
	echo 'Difficulty: '.$cvt_xml['difficulty'] .'<br>';
	?><div style="background-color:#000000; border:1px dotted black; width:300px"><?php 
	foreach( $cvt_xml['player'] as $plr )
	{
		echo '<font class="'.$plr['class'].'">'.$plr['name'].'</font>, ';
	}
	?></div>
	<?php foreach( $cvt_xml['kills'] as $boss )
	{
		echo $boss['name'].' - '.$boss['time'].'<br>';
	}
	
	$url = 'http://eu.wowarmory.com/wow-icons/_images/21x21/';
	foreach( $cvt_xml['loot'] as $item )
	{
		$tmp = explode(':', $item['itemid']);
		?><a href="http://wowhead.com/?item=<?php echo $tmp[0]?>" target="_new">
		<img src="<?php echo $url.strtolower($item['icon'])?>.png">
		<font color="<?php echo $item['color']?>"><strong><?php echo $item['itemname']?></strong></font>
		<?php $item['costs'].'@'.$item['player'];?>
		</a><br><?php 
	}
	?><br><b>XML Tracker Log:</b><br><textarea rows="20" cols="75"><?php echo $xml?></textarea>
	<?php fprint_r($cvt_xml)?>