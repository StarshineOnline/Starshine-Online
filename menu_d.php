	<div id="droite">
		<div class="sousmenu">
		<?php
		if(array_key_exists('ID', $_SESSION))
		{
		?>
				<div class="hautsousmenu">
					Vos stats
				</div>
				<div class="milieusousmenu">
					<table>
						<tr>
							<td width="40%">
								Niveau
							</td>
							<td>
								: <?php echo $joueur['level']; ?>
							</td>
						</tr>
						<tr>
							<td>
								Grade
							</td>
							<td>
								: <?php echo $joueur['grade']; ?>
							</td>
						</tr>
						<tr>
							<td>
								Stars
							</td>
							<td>
								: <?php echo $joueur['star']; ?>
							</td>
						</tr>
						<tr>
							<td>
								Joueurs tués
							</td>
							<td>
								: <?php echo $joueur['frag']; ?>
							</td>
						</tr>
						<tr>
							<td>
								Nombre de mort
							</td>
							<td>
								: <?php echo $joueur['mort']; ?>
							</td>
						</tr>
					</table>
				</div>
			<?php
			}
			?>
				<div class="hautsousmenu">
					Pub
				</div>

				<div class="milieusousmenu">
				<script type="text/javascript">
				google_ad_client = "pub-7541997421837440";
				google_ad_width = 120;
				google_ad_height = 600;
				google_ad_format = "120x600_as";
				google_ad_type = "text_image";
				google_ad_channel = "";
				google_color_border = "e4eaf2";
				google_color_bg = "e4eaf2";
				google_color_link = "0000FF";
				google_color_text = "000000";
				google_color_url = "008000";
				</script>
				<script type="text/javascript"
				  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
				</script>
				</div>
			</div>
		</div>