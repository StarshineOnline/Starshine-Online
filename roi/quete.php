<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
require('haut_roi.php');

?>
<?php
if($joueur->get_rang_royaume() != 6)
	echo '<p>Cette page vous est interdit</p>';
else if($_GET['action'] == 'achat')
{
	//Récupère les informations sur la quète
	$requete = "SELECT * FROM quete WHERE id = ".sSQL($_GET['id']);
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	//Vérifie que le royaume a assez de stars pour l'acheter
	if($royaume->get_star() >= $row['star_royaume'])
	{
		//Ajout de la quète dans la liste des quètes du royaume
		$requete = "INSERT INTO quete_royaume VALUES('', ".$royaume->get_id().", ".$row['id'].")";
		$req = $db->query($requete);
		//Mis a jour des stars du royaume
		$requete = "UPDATE royaume SET star = star - ".$row['star_royaume']." WHERE ID = ".$royaume->get_id();
		$req = $db->query($requete);
		echo '<h6>Votre royaume a bien acheté la quète "'.$row['nom'].'</h6>';
	}
	else
	{
		echo '<h5>Votre royaume n\'a pas assez de stars pour acheter cette quète.</h5>';
	}
	?>
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
	<a onclick="envoiInfo('quete.php?direction=quete&amp;action=achat&amp;id=<?php echo $row['id']; ?>','message_confirm');envoiInfo('quete.php','contenu_jeu');refresh('perso_contenu.php','perso_contenu');$('#popup').hide();">Acheter cette quète</a><br />
	<br />
	<?php
}
else
{
	$req = $db->query("SELECT * FROM quete WHERE quete.achat = 'oui' AND id NOT IN (SELECT id_quete FROM quete_royaume WHERE id_royaume = ".$royaume->get_id().") AND star_royaume<".$royaume->get_star()." ORDER BY lvl_joueur");
	echo "<div id='quete'>";
	?>
	<ul>
	<li class='haut'>
			<span class='quete_nom'>Nom</span>
			<span class='quete_niveau'>Level</span>
			<span class='quete_groupe'>Mode</span>
			<span class='quete_repetable'>Répétable</span>
			<span class='quete_cout'>Coût</span>
			<span class='quete_star'>Stars</span>
			<span class='quete_exp'>Expérience</span>
			<span class='quete_honneur'>Honneur</span>
	</li>
<?php
	$class = 't1';
	while($quete = $db->read_object($req))
	{
		$href = 'poscase='.$W_case.'&amp;direction=quete&amp;action=voir&amp;id='.$quete->id;
		//$js = 'new Tip(\'quete_'.$row['id'].'\', \'content\');';
		echo "
		<li class='$class'>
			<span class='quete_nom'>".$quete->nom."</span>
			<span class='quete_niveau'>".$quete->lvl_joueur."</span>
			<span class='quete_groupe'>".$quete->mode."</span>
			<span class='quete_repetable'>".$quete->repete."</span>
			<span class='quete_cout'>".$quete->star_royaume."</span>
			<span class='quete_star'>".$quete->star."</span>
			<span class='quete_exp'>".$quete->exp."</span>
			<span class='quete_honneur'>".$quete->honneur."</span>
			<span class='quete_achat'><a onclick=\"affichePopUp('quete.php?".$href."');\" id='quete_".$quete->id."'>Détails de la quète</a></span>
		</li>";
		if ($class == 't1'){$class = 't2';}else{$class = 't1';}		
	}
	echo "</ul></div>";
?>

<?php
}
?>
