<?php
include('haut.php');
include('haut_site.php');
if ($maintenance)
{
	echo 'Starshine-online est actuellement en refonte compl�te, l\'exp�rience acqu�rie gr�ce � l\'alpha m\'a permis de voir les gros probl�mes qui pourraient se poser.<br />
	Je vais donc travailler sur la b�ta.<br />';
}
else
{
	if (isset($_POST['log']))
	{
		//On recherche dans la bdd les informations du joueur
		$requete = "SELECT ID, nom, password, x, y FROM perso WHERE nom LIKE '".$_POST['nom']."'";
		$req = $db->query($requete);
		$row = $db->read_array($req);
		if ($row != '')
		{
			$password_base = $row['password'];
			$ID_base = $row['ID'];
			$x = $row['x'];
			$y = $row['y'];
			if (md5($_POST['password']) == $password_base)
			{
				$position_du_joueur = convert_in_pos($x, $y);
				//Enregistrement du nom, de l'id, de la position du joueur dans la session
				$_SESSION['nom'] = $_POST['nom'];
				$_SESSION['ID'] = $ID_base;
				$_SESSION['position'] = $position_du_joueur; 
			}
			else
			{
				echo 'Erreur de mot de passe.';
			}
		}
		else
		{
			echo 'Pseudo inconnu.';
		}
	}
	elseif (isset($_GET['deco']))
	{
		session_unregister('nom');
		session_unregister('ID');
		unset($_SESSION['nom']);
		unset($_SESSION['ID']);
		unset($_SESSION['position']); 
	}
	include('menu.php');
	?>
	<?php
	//Connect�
	if (isset($_SESSION['nom']))
	{
		echo '
		<div id="jouer">
			<p style="text-align : center;"><a href="jeu2.php">Cliquez ici pour acc�der au jeu.</a></p>
		</div>';
	}
	?>
		<div id="presentation">
			<div class="titre">
				Pr�sentation
			</div>
			Bienvenue dans le monde de Starshine-Online.<br />
			Pour l'instant au stade d'alpha (c'est � dire en phase de cr�ation et de test interne), starshine-online sera un jeu de r�le massivement mutijoueur en tour par tour.<br />
			Il vous permettra d'entrer dans la peau d'un grand h�ros de l'univers Starshine peupl� de nombreuses cr�atures et d'autres h�ros ennemis pr�s a tout pour d�truire votre peuple.<br />
		</div>
		<div id="news">
			<div class="titre">
				L'interface
			</div>
			<img src="image/aide.jpg"><br />
			<h3>1- Carte du monde</h2>
			Cette petite carte de 7 * 7 cases repr�sente le monde qui vous entoure, chaque case � une coordonn�e X et Y qui vous permettra de vous situer dans le monde global.<br />
			Votre position est indiqu�e par les coordon�es en rouge, et vous devez vous trouver au centre de la carte, represent� par un petit personnage symbolisant votre race.<br />
			Tout autour de vous, vous pouvez distinguer les diff�rents types de terrains (ville, plaine, for�t, etc.), les autres joueurs (symbolis�s par des petits personnages de leur race), ainsi que les monstres alentours.<br />
			En laissant votre souris sur une case, vous pouvez avoir plus d'informations sur les joueurs et monstres qui sont sur cette case.<br />
			Pour avoir encore de plus ample informations sur la case, cliquez dessus et � droite s'affichera de nouvelles informations que l'on verra plus en d�tails plus tard.
			<h3>2- D�placement</h2>
			Pour vous d�placer dans le monde de Starshine, rien de plus simple, utilisez la petite boussole, le co�t de PA en d�placement est indiqu� lorsque vous cliquez sur une case dans les informations de celle-ci. Notez qu'un d�placement en diagonale vous coutera un PA de plus qu'indiqu� sur la case.<br />
			Autre chose importante de cette boussole, en cliquant au milieu de celle-ci, vous afficherez automatiquement la carte, pratique lorsque vous avez fini de naviguez dans les menus de la ville.
			<h3>3- Informations personnage</h2>
			Cette partie regroupe toutes les informations interessantes de votre personnage.<br />
			Premi�rement, son grade dans le royaume suivi de son nom. Ensuite sa race, sa classe et son niveau. Et pour finir une liste de buffs qu'il a actuellement. (pour obtenir des infos, laisser votre souris sur le buff)<br />
			Deuxi�mement, ses Points d'actions, points de vies et points de mana (PA / HP / MP), repr�sent�s par des barres color�es qui se remplisses ou se vides.<br />
			Ensuite, un petit r�sum� de son honneur, de son exp�rience et de ses stars.<br />
			Pour finir, les informations sur son groupe, avec la liste des membres ainsi que leur points de vie et de mana.<br />
			<h3>4- Informations sur le royaume</h2>
			Dans les informations sur le royaume, on retrouve plusieurs choses importantes.<br />
			Tout d'abord, dans quel royaume nous nous trouvons, suivi de notre attitude diplomatique envers ce royaume, ainsi que le taux de taxe en cours pour nous dans ce royaume.<br />
			Un peu plus bas, si nous nous trouvons dans une ville, nous pouvons acc�der � un menu sp�cial pour la ville permettant d'acheter, de vendre, de prendre des qu�tes, etc, via le lien "acc�der � la ville".
			Tandis que le lien "acc�der � la map" permet de revenir � l'affichage normal de la carte.<br />
			Toujours plus bas, nous pouvons savoir de quel terrain il sagit, et combien cela nous coutera en PA pour se d�placer d'une case adjacente vers celle-ci.
			<h3>5- Informations sur les joueurs et monstres</h2>
			<h3>6- Menus</h2>
		</div>
		<div id="news">
			<div class="titre">
				Les taxes
			</div>
			<?php
			$roy = get_royaume_info('barbare', 7);
			$royv = get_royaume_info('vampire', 7);
			$roys = get_royaume_info('scavenger', 7);
			$diplomatie[0] = 'Alliance fraternelle';
			$diplomatie[1] = 'Alliance';
			$diplomatie[2] = 'Paix durable';
			$diplomatie[3] = 'Paix';
			$diplomatie[4] = 'En bons termes';
			$diplomatie[5] = 'Neutre';
			$diplomatie[6] = 'Mauvais termes';
			$diplomatie[7] = 'Guerre';
			$diplomatie[8] = 'Guerre durable';
			$diplomatie[9] = 'Ennemis';
			$diplomatie[10] = 'Ennemis eternels';
			?>
			Les taxes s'appliquent sur les t�l�portations et les objets achet�s en ville.<br />
			Le calcul est fait de la fa�on suivante pour d�finir le taux de taxe :<br />
			Chaque royaume a un taux de taxe de base, prenons par exemple un joueur barbare en visite dans la ville du royaume des nains.<br />
			Le taux de taxe de base du royaume des nains est de <?php echo $roy['taxe_base']; ?>%.<br />
			Comme les nains et les barbares sont en terme diplomatiques de "<?php echo $diplomatie[$roy['diplo']]; ?>", la taxe est r�duite � <?php echo $roy['taxe']; ?>%.<br />
			Pour un vampire en royaume nain (<?php echo $diplomatie[$royv['diplo']]; ?>), la taxe aurait �t� de <?php echo $royv['taxe']; ?>%, et pour un scavenger (<?php echo $diplomatie[$roys['diplo']]; ?>) de <?php echo $roys['taxe']; ?>%.
		</div>
	<?php
}

	?>
	</div>
	<?php
include('bas.php');

?>