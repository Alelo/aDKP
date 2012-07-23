<?php
	require_once('func/get_function.func.php');
	get_function('str_extract');
	if( isset( $_POST['import_btn'] ) )
	{
		$text = str_extract( $_POST['import_xml'], '<key>', '</key>' );
		$text = str_replace( ':' ,'' ,$text );
		$text = str_replace( '/', '.', $text );
		$text = str_replace( ' ', '_', $text );
		$file = fopen('./xml/'.$text.'.xml','w');
		fwrite($file, $_POST['import_xml']);
		?><form action="addtodb.php" method="post">
		<input type="text" name="name" value="<?php echo $text;?>" id="some_name" readonly="true"><br/>
		<input type="hidden" name="file" value="<?php echo $_POST['import_xml'];?>" id="file">
		Klicken Sie auf den Button um den XML-Parser zu starten<br><input value="Upload" name="upload" type="submit" />
		</form><?php 
	}
?>
<form method="post">
	<table>
		<tr>
			<td>
				XML Inhalt:
			</td>
		</tr>
		<tr>
			<td>
				<textarea cols="160" rows="40" name="import_xml"><?php echo $_POST['import_xml']; ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" value="Import" name="import_btn" />
			</td>
		</tr>
	</table>
</form>