<?php
	require_once('func/getlvbycolor.func.php');
	function ItemLink($item_id, $item_icon, $item_quality, $item_name){
		$link = '<a target="_new" href="http://wowhead.com?item='.$item_id.'">';
		$link .= '<img border="0" src="http://static.wowhead.com/images/wow/icons/small/'.$item_icon.'.jpg" alt="'.$item_name.'" />';
		$link .= '</a>';
		$link .= '<a href="/itemhistory/'.$item_id.'" class="'.getLvByColor($item_quality).' bold" style="text-decoration:none" rel="item='.$item_id.'">'.$item_name.'</a>'."\n";
		return $link;
	}
?>