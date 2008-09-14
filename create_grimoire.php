<?php // -*- tab-width:	 2 -*-
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR');
include('haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include('menu_admin.php');
	echo '
	<div id="contenu">
	<div id="centre3">
	<div class="titre">
				Création d\'un monstre
	</div>				
	';

	$jeu = '<option value=""></option>';
	$req = $db->query("select nom from comp_jeu");
	while ($row = $db->read_assoc($req)) {
		$jeu .= '<option value="'.$row['nom'].'">'.$row['nom'].'</option>';
	}

	$combat = '<option value=""></option>';
	$req = $db->query("select nom from comp_combat");
	while ($row = $db->read_assoc($req)) {
		$combat .= '<option value="'.$row['nom'].'">'.$row['nom'].'</option>';
	}

	$joueur = '<option value=""></option>';
	$req = $db->query("select distinct competence from classe_permet where new = 'yes' and permet > 1");
	while ($row = $db->read_assoc($req)) {
		$joueur .= '<option value="'.$row['competence'].'">'.
			$row['competence'].'</option>';
	}

	$req = $db->query("select nom from classe");
	$cnt = 0;
	while ($row = $db->read_assoc($req)) {
		$reserve .= '<label><input type="checkbox" name="resrve[]" value="'.
			$row['nom'].'" />'.$row['nom'].'</label> ';
		if ($cnt++ > 2) {
			$reserve .= '<br />';
			$cnt = 0;
		}
	}


	//Insertion du grimoire dans la bdd
	if(array_key_exists('submit', $_POST))
	{
	  //echo '<pre>'; var_dump($_POST); echo '</pre>';
		if ($_POST['type'] == "combat") {
			$combat = "'".$_POST['combat']."'";
			$jeu = 'null';
			$joueur_id = 'null';
			$joueur_comp = 'null';
			$joueur_val = 'null';
		} elseif ($_POST['type'] == "jeu") {
			$combat = 'null';
			$jeu = "'".$_POST['jeu']."'";
			$joueur_id = 'null';
			$joueur_comp = 'null';
			$joueur_val = 'null';
		} elseif ($_POST['type'] == "joueur") {
			$combat = 'null';
			$jeu = 'null';
			$joueur_id = 1;
			$joueur_comp = "'".$_POST['comp_joueur_competence']."'";
			$joueur_val = "'".$_POST['comp_joueur_valueadd']."'";
		}
		if (isset($_POST['resrve'])) {
			$classe = "'".implode(';', $_POST['resrve'])."'";
			$classe = mb_strtolower($classe);
		} else {
			$classe = 'null';
		}
		$cols = "nom, comp_jeu, comp_combat, comp_perso_id, ".
			"comp_perso_competence, comp_perso_valueadd, classe_requis";
		$requete = "insert into grimoire ($cols) values ('$_POST[nom]', ".
			"$jeu, $combat, $joueur_id, $joueur_comp, $joueur_val, $classe)";

		//echo $requete;
		$db->query($requete);
	}
	?>
<script type="text/javascript" language="javascript">
function hide_all() {
  var c = document.getElementById("tr_cbt");
  c.style.visibility = "hidden";
  var j = document.getElementById("tr_jeu");
  j.style.visibility = "hidden";
  var p = document.getElementById("tr_joueur");
  p.style.visibility = "hidden";
}
function show_combat() {
  hide_all();
  var c = document.getElementById("tr_cbt");
  c.style.visibility = "visible";
}
function show_jeu() {
  hide_all();
  var j = document.getElementById("tr_jeu");
  j.style.visibility = "visible";
}
function show_joueur() {
  hide_all();
  var p = document.getElementById("tr_joueur");
  p.style.visibility = "visible";
}
</script>
<form action="create_grimoire.php" method="post">
<table class="admin">
<tr>
	<td>
		Titre
	</td>
	<td>
		<input type="text" name="nom" />
	</td>
	<td>
		Type
	</td>
	<td>
		<label onClick="javascript:show_combat();"><input type="radio" name="type" value="combat" />Compétence&nbsp;de&nbsp;combat</label><br />
		<label onClick="javascript:show_jeu();"><input type="radio" name="type" value="jeu" />Compétence&nbsp;hors&nbsp;combat</label><br />
		<label onClick="javascript:show_joueur();"><input type="radio" name="type" value="joueur" />Compétence&nbsp;active</label>
	</td>
</tr>
<tr id="tr_cbt" style="visibility: hidden">
	<td>
		Compétence :
	</td>
	<td>
		<select name="comp_combat">
				<?= $combat ?>
		</select>
	</td>
</tr>
<tr id="tr_jeu" style="visibility: hidden">
	<td>
		Compétence :
	</td>
	<td>
		<select name="comp_jeu">
				<?= $jeu ?>
		</select>
	</td>
</tr>
<tr id="tr_joueur" style="visibility: hidden">
	<td>
		Compétence :
	</td>
	<td>
		<select name="comp_joueur_competence">
				<?= $joueur ?>
		</select>
	</td>
	<td>
		Valeur :
	</td>
	<td>
		<input name="comp_joueur_valueadd" />
	</td>
</tr>
<tr>
	<td>
		Réservé aux
	</td>
	<td colspan="6">
		<?= $reserve ?>
	<td>
<?php

?>
	</td>
</tr>
</table>
<input type="submit" name="submit" value="Créer le grimoire" />
<form>
		<?php
}
?>