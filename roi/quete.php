<?php
require('haut_roi.php');

?>
<h3>Gestion des Quêtes</h3>
<?php
if($joueur->get_rang_royaume() != 6)
	echo '<p>Cheater</p>';
else if($_GET['action'] == 'achat')
{
	//Récupère les informations sur la quète
	$requete = "SELECT * FROM quete WHERE id = ".sSQL($_GET['id']);
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	//Vérifie que le royaume a assez de stars pour l'acheter
	if($R['star'] >= $row['star_royaume'])
	{
		//Ajout de la quète dans la liste des quètes du royaume
		$requete = "INSERT INTO quete_royaume VALUES('', ".$R['ID'].", ".$row['id'].")";
		$req = $db->query($requete);
		//Mis a jour des stars du royaume
		$requete = "UPDATE royaume SET star = star - ".$row['star_royaume']." WHERE ID = ".$R['ID'];
		$req = $db->query($requete);
		echo 'Votre royaume a bien acheté la quète "'.$row['nom'].'"';
	}
	else
	{
		echo 'Votre royaume n\'a pas assez de stars pour acheter cette quète.';
	}
	?>
	<br /><a href="quete.php?direction=quete" onclick="return envoiInfo(this.href, 'conteneur')">Retour au menu des quètes</a>
	<?php
}
elseif($_GET['action'] == 'voir')
{
	//Récupère les informations sur la quète
	$requete = "SELECT * FROM quete WHERE id = ".sSQL($_GET['id']);
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	?>
	<h3 style="margin-bottom : 3px;""><?php echo $row['nom']; ?></h3>
	<span style="font-style : italic;">Niveau conseillé <?php echo $row['lvl_joueur']; ?><br />
	Répétable : <?php if($row['repete'] == 'y') echo 'Oui'; else echo 'Non'; ?><br />
	<?php if($row['mode'] == 'g') echo 'Groupe'; else echo 'Solo'; ?></span><br />
	<br />
	<?php echo nl2br($row['description']); ?>
	<h3>Requis</h3>
	<ul>
		<li>Niveau requis : <?php echo $row['niveau_requis']; ?></li>
		<li>Honneur requis : <?php echo $row['honneur_requis']; ?></li>
		<?php
		if($row['quete_requis'] != '')
		{
			$qrequis = explode(';', $row['quete_requis']);
			foreach($qrequis as $qid)
			{
				$requete = "SELECT nom FROM quete WHERE id = ".$qid;
				$qreq = $db->query($requete);
				$qrow = $db->read_assoc($qreq);
				?>
			<li>Avoir fini la quète : <?php echo $qrow['nom']; ?></li>
				<?php
			}
		}
		?>
	</ul>
	<h3>Récompense</h3>
	<ul>
		<li>Stars : <?php echo $row['star']; ?></li>
		<li>Expérience : <?php echo $row['exp']; ?></li>
		<li>Honneur : <?php echo $row['honneur']; ?></li>
		<li><strong>Objets</strong> :</li>
		<?php
		$rewards = explode(';', $row['reward']);
		$r = 0;
		while($r < count($rewards))
		{
			$reward_exp = explode('-', $rewards[$r]);
			$reward_id = $reward_exp[0];
			$reward_id_objet = mb_substr($reward_id, 1);
			$reward_nb = $reward_exp[1];
			switch($reward_id[0])
			{
				case 'r' :
					$requete = "SELECT * FROM recette WHERE id = ".$reward_id_objet;
					$req_r = $db->query($requete);
					$row_r = $db->read_assoc($req_r);
					echo '<li>Recette de '.$row_r['nom'].' X '.$reward_nb.'</li>';
				break;
				case 'x' :
					echo '<li>Objet aléatoire</li>';
				break;
			}
			$r++;
		}
		?>
	</ul>
	<h3>Cout pour le royaume : <?php echo $row['star_royaume']; ?> stars</h3>
	<br />
	<a href="quete.php?direction=quete&amp;action=achat&amp;id=<?php echo $row['id']; ?>" onclick="return envoiInfo(this.href, 'conteneur')">Acheter cette quète</a><br />
	<br />
	<a href="quete.php?direction=quete" onclick="return envoiInfo(this.href, 'conteneur')">Retour à la liste des quètes</a><br />
	<?php
}
else
{
	$requete = "SELECT * FROM quete WHERE quete.achat = 'oui' AND id NOT IN (SELECT id_quete FROM quete_royaume WHERE id_royaume = ".$R['ID'].") ORDER BY star_royaume";
	$req = $db->query($requete);

?>
	<table>
	<tr>
		<td>
			Nom
		</td>
		<td>
			Coût en stars
		</td>
		<td>
			Achat
		</td>
	</tr>
<?php
	while($row = $db->read_array($req))
	{
		$href = 'quete.php?poscase='.$W_case.'&amp;direction=quete&amp;action=voir&amp;id='.$row['id'];
		$js = 'new Tip(\'quete_'.$row['id'].'\', \'content\');
	';
		echo '
		<tr>
			<td>
				'.$row['nom'].'
			</td>
			<td>
				'.$row['star_royaume'].'
			</td>
			<td>
				<a href="'.$href.'" onclick="return envoiInfo(this.href, \'conteneur\');" id="quete_'.$row['id'].'">Détails de la quète</a>
			</td>
		</tr>';
	}
?>
</table>
<?php
}
?>
