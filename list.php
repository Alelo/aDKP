					<table class="dkp" id="dkplisting">
						<tr>
							<td align="center" colspan="10" class="bold">
								Raid DKP Listing
							</td>
						</tr>
						<tr id="dkplist">
						<?php
							get_function(array('dev','class_de','race_de'));
							
							$mysqli = new mysqli(DB_HOST, DB_USER,DB_PW, DB_DB);
							if (mysqli_connect_errno()) {
						    	printf("Connect failed: %s\n", mysqli_connect_error());
						    	exit();
							};
							$sqlchars = "SELECT characters.char_name, redundant_dkp.dkp, characters.char_class FROM characters, redundant_dkp WHERE characters.char_id = redundant_dkp.char_id AND hide_char=0 ORDER BY redundant_dkp.dkp DESC";
							$resultchars = $mysqli->query($sqlchars);
							$sqldkp = "SELECT * FROM `redundant_dkp`";
							$resultdkp = $mysqli->query($sqldkp);
							//arrays+befuellung
							$chars = Array(
											'DRUID' => Array(),
											'WARLOCK' => Array(),
											'HUNTER' => Array(),
											'WARRIOR' => Array(),
											'MAGE' => Array(),
											'PALADIN' => Array(),
											'PRIEST' => Array(),
											'SHAMAN' => Array(),
											'ROGUE' => Array(),
											'DEATHKNIGHT' => Array()
							);
							while($rowchars = $resultchars->fetch_assoc()){
								array_push($chars[$rowchars['char_class']], $rowchars);
							};
							$mysqli->close();
							//ausgabe
							foreach( $chars as $perclass ){ ?>
							<td valign="top">
								<table class="dkp" style="height: 100%;">
									<tr>
										<td colspan="2" align="center">
											<a href="/class/<?php echo strtolower($perclass[0]['char_class']) ?>">
												<span class="<?php echo htmlentities($perclass[0]['char_class']);?> bold"><?php echo class_de($perclass[0]['char_class']);?></span>
											</a>
										</td>
									</tr>
									<?php foreach( $perclass as $plr ){?>
									<tr>
										<td>
											<a href="/character/<?php echo htmlentities($plr['char_name']);?>" class="player">
												<span class="<?php echo $plr['char_class'];?>"><?php echo htmlentities($plr['char_name']);?></span>
											</a>
										</td>
										<td align="right">
											<span style="color: <?php echo (($plr['dkp'] >= 0) ? '#33cc33' : '#E23B30'); ?>" class="dkpvalue"><?php echo $plr['dkp'];?></span>
										</td>
									</tr>
									<?php }?>
								</table>
							</td>
							<?php };?>
						</tr>
					</table>