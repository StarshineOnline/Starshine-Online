<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
require_once(root.'roi/haut_roi.php');
?>
  		<div id='boutique'>
  		<ul>	
  		<li class='haut' style='height:30px !important;line-height:30px !important;'>
  			<span class='boutique_nom'>ID</span>
			<span class='boutique_nom'>Nom</span>
  			<span class='boutique_nom'>Leader</span>
			<span class='boutique_nom'>Nombre</span>
  			<span class='boutique_nom'>Partage</span>
  			<span class='boutique_nom'>Level</span>
  			<span class='boutique_nom'>Bataille</span>
  		</li>

<?php
$requete = "SELECT groupe.id as groupeid, groupe_joueur.id_joueur, perso.nom, perso.race FROM groupe LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.ID WHERE groupe_joueur.leader = 'y' AND perso.race = '".$joueur->get_race()."'";
$req = $db->query($requete);
$boutique_class = 't1';
$partages = array(array('r', 'Aléatoire'), array('t', 'Par tour'), array('l', 'Leader'), array('k', 'Trouve = Garde'));
while($row = $db->read_assoc($req))
{
	$groupe = new groupe($row['groupeid']);
	$leader = new perso($groupe->get_id_leader());
	$bataille_groupe = new bataille_groupe(0,0,$row['groupeid']);
	if($bataille_groupe->is_bataille()) 
	{
		$bataille = new bataille($bataille_groupe->id_bataille);
		$nom = $bataille->nom;
	}
	else
		$nom = "Aucune";
	foreach($partages as $part)
	{
		if($groupe->get_partage() == $part[0])
			$partage = $part[1];
	}
	?>
	<li class='<?php echo $boutique_class; ?>' id="groupe_<?php echo $groupe->get_id(); ?>" onclick="affichePopUp('infos_groupe.php?id_groupe=<?php echo $groupe->get_id(); ?>');">
		<span class='boutique_nom'><?php echo $groupe->get_id(); ?></span>
		<span class='boutique_nom'><?php echo $groupe->get_nom(); ?></span>
		<span class='boutique_nom'><?php echo $leader->get_nom(); ?></span>
		<span class='boutique_nom'><?php echo ($G_nb_joueur_groupe + 1 - $groupe->get_place_libre()).' / '.($G_nb_joueur_groupe+1); ?></span>
		<span class='boutique_nom'><?php echo $partage; ?></span>
		<span class='boutique_nom'><?php echo $groupe->get_level();?></span>
		<span class='boutique_nom'><?php echo $nom;?></span>
	</li>
	<?php
	if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}
}
?>
</ul>
</div>


<div id="infos_groupe" style="text-align: center;">
	Cliquez sur un groupe pour obtenir des informations
</div>