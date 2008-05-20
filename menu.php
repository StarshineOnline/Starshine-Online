<?php

if ($maintenance)
{
}
else
{	
	?>
	
		<div id="menuindex">
			<div class="sousmenu">
			
				<div class="hautsousmenu">
						Connexion
				</div>
				<div class="milieusousmenu">
	<?php
	
	if (!isset($_SESSION['nom']))  echo 'Login';
	else
	{
		$joueur = recupperso($_SESSION['ID']);		
		$joueur = check_perso($joueur);
		echo $_SESSION['nom'].' - Niv '.$joueur['level'].'<br />
		PA  = '.$joueur['pa'].' / '.$G_PA_max.'<br />
		HP '.genere_image_hp($joueur).'<br />
		MP '.genere_image_mp($joueur).'<br />';
	}
	
	if (!isset($_SESSION['nom']))
	{
	?>
	
		<form action="" method="post">
			<table cellspacing="0">
			<tr>
				<td>
					ID 
				</td>
				<td>
					: <input type="text" name="nom" size="10" class="input" />
				</td>
			</tr>
			<tr>
				<td>
					Pass
				</td>
				<td>
					: <input type="password" name="password" size="10" class="input" />
				</td>
			</tr>
			<tr>
				<td colspan="2" style="vertical-align : top;">
					Auto Login <input type="checkbox" name="auto_login" value="Ok" />
				</td>
			</tr>
			</table>
			<input type="submit" name="log" value="Connexion" class="input" />
		</form>
		
	<?php
	}
	else
	{
		$time = time();
		?>
		
		<ul>
			<li><a href="<?php echo $root; ?>jeu2.php">Jouer</a></li>
			<li><a href="<?php echo $root; ?>option.php">Options</a></li>
			<li><a href="<?php echo $root; ?>index.php?deco=ok">Deconnexion</a></li>
		</ul>
		<?php
	}
	
	?>
				</div>
			</div>
			<div class="sousmenu">
				<div class="hautsousmenu">
					StarShine v<?php echo $G_version; ?>
				</div>
				<div class="milieusousmenu">
					<ul class="listemenu">
	<?php
		if (!isset($_SESSION['nom']))
		{
	?>
						<li><a href="<?php echo $root; ?>create.php">Créer un perso</a></li>
	<?php
		}
	?>
						<li><a href="<?php echo $root; ?>background.php">Background</a></li>
						<li><a href="<?php echo $root; ?>diplomatie.php">Diplomatie</a></li>
						<li><a href="<?php echo $root; ?>classement.php">Classements</a></li>
						<li><a href="<?php echo $root; ?>royaume.php">Cartes</a></li>
						<li><a href="<?php echo $root; ?>liste_monstre.php">Bestiaire</a></li>
						<li><a href="<?php echo $root; ?>stats.php">Statistiques</a></li>

					</ul>
				</div>
			</div>
		<div class="sousmenu">
			<div class="hautsousmenu">
				Communauté
			</div>
			<div class="milieusousmenu">
				<ul class="listemenu">
					<li><a href="http://wiki.starshine-online.com">Aide des joueurs (wiki)</a></li>
					<li><a href="http://forum.starshine-online.com">Forum</a></li>
					<li><a href="<?php echo $root; ?>acces_chat.php">Tchat</a></li>
				</ul>
			</div>
		</div>
		<div class="sousmenu">
			<div class="hautsousmenu">
				Bugs
			</div>
			<div class="milieusousmenu">
				<ul class="listemenu">
					<li><a href="http://bug.starshine-online.com/">Liste des bugs</a></li>
					<li><a href="http://bug.starshine-online.com/newreport.php">Signaler un bug</a></li>
				</ul>
			</div>
		</div>
		
	</div>


<?php
}
?>