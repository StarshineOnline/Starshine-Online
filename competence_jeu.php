<?php // -*- mode: php; tab-width:2 -*-
if (file_exists('root.php'))
include_once('root.php');

include_once ('livre.php');
$tab_sort_jeu = explode(';', $joueur->get_comp_jeu());
?>
<hr>
<?php
if($joueur->get_groupe() != 0) $groupe_joueur = new groupe($joueur->get_groupe()); else $groupe_joueur = false;
if (isset($_GET['ID']))
{
	$joueur->check_comp_jeu_connu($_GET['ID']);
	$comp = comp_jeu::factory( sSQL($_GET['ID'], SSQL_INTEGER) );
	$requete = "SELECT * FROM comp_jeu WHERE id = ".sSQL($_GET['ID'], SSQL_INTEGER);
	//echo $requete;
	$req = $db->query($requete);

	$row = $db->read_array($req);
	if(array_key_exists('groupe', $_GET) AND $_GET['groupe'] == 'yes') $groupe = true; else $groupe = false;
	$sortpa = $comp->get_pa($joueur);//round($row['pa']);
	$sortmp = $comp->get_mp();//round($row['mp']);
	$action = false;
	$cibles = array($joueur->get_id());
	if($joueur->get_pa() < $sortpa)
	{
		echo '<h5>Pas assez de PA</h5>';
	}
	elseif($joueur->get_mp() < $sortmp)
	{
		echo '<h5>Pas assez de mana</h5>';
	}
	elseif($joueur->is_buff('petrifie'))
	{
		echo '<h5>Vous êtes pétrifié, vous ne pouvez pas utiliser de compétence.</h5>';
	}
	else
	{
		if( $comp->lance($joueur) )
		{
				$joueur->set_pa($joueur->get_pa() - $sortpa);
				$joueur->set_mp($joueur->get_mp() - $sortmp);
				$joueur->sauver();
		}
	}
	echo '<br /><a href="competence_jeu.php" onclick="return envoiInfo(this.href, \'information\');">Revenir au livre des compétences</a>';
}
else
{
	$i = 0;
	$type = '';
	$magies = array();
	$magie = '';
	$requete = "SELECT * FROM comp_jeu GROUP BY comp_assoc";
	$req = $db->query($requete);
	while($row = $db->read_array($req))
	{
		if($magie != $row['comp_assoc'])
		{
			$magie = $row['comp_assoc'];
			$magies[] = $row['comp_assoc'];
		}
	}
?>
<div id="livre">
	<div id="livre_gauche_physique">
	<?php
	$k=1;
	foreach($magies as $magie)
	{
		echo '<div id="livre_physique_icone'.$k.'"><a href="competence_jeu.php?tri='.$magie.'" onclick="return envoiInfo(this.href, \'information\');"><img src="image/'.$magie.'.png" alt="'.$Gtrad[$magie].'" title="'.$Gtrad[$magie].'" onmouseover="this.src = \'image/icone/'.$magie.'hover.png\'" onmouseout="this.src = \'image/'.$magie.'.png\'"/></a></div>';
		$k++;
	}
	?>
	</div>
	<div id="livre_haut"><h3>Livre</h3></div>
	<div id="livre_corps">
		<ul>
<?php
	if ('champion' == $joueur->get_classe() AND !array_key_exists('tri', $_GET))
	{
		$where = "WHERE comp_assoc = 'melee'";
	}
	else
	{
		$where = 'WHERE comp_assoc = \''.sSQL($_GET['tri']).'\'';
	}
	$requete = "SELECT * FROM comp_jeu ".$where." ORDER BY comp_assoc ASC, type ASC, nom ASC";
	$req = $db->query($requete);
	$magie = '';

	while($row = $db->read_array($req))
	{
		if($magie != $row['comp_assoc'])
		{
			$magie = $row['comp_assoc'];
			echo '<li style="height:3px !important; background: none !important; margin-top:0px !important;"><strong>'.$Gtrad[$magie].'</strong></li>';
		}
		if(in_array($row['id'], $tab_sort_jeu))
		{
			$href = 'return envoiInfo(\'competence_jeu.php?ID='.$row['id'].'\', \'information\')';
			$cursor = 'cursor : pointer;';
			$color = '#444';
			$echo = addslashes(description($row['description'], $row));
			echo '<li><span class="livre_ligne" style="float:left; width:150px; '.$cursor.'text-decoration : none; color : '.$color.';" onclick="nd();'.$href.';" onmouseover="return '.make_overlib($echo).'" onmouseout="return nd();"> <strong>'.$row['nom'].'</strong></span>';
			?>
			<span class="xsmall" style="float: right; padding-right:15px;">(<?php echo $row['mp']; ?> MP - <?php echo $row['pa']; ?>PA)</span>
			<span style="position:relative;bottom:40px;left:133px;"><img src="image/interface/livres/ficellefavori.png" /></span>
			</li>
			<?php
			$i++;
		}
	}
	?>
		</ul>
	</div>
	<div id="livre_bat"></div>
</div>
<?php
}

print_reload_area('infoperso.php?javascript=oui', 'perso');
?>

