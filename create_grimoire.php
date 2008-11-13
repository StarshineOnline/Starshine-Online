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
				Création d\'un grimoire
	</div>				
	';

	$jeu = '<option value=""></option>';
	$req = $db->query("SELECT id, nom FROM comp_jeu ORDER BY nom ASC");
	while ($row = $db->read_assoc($req)) {
		$jeu .= '<option value="'.$row['id'].'">'.$row['nom'].'</option>';
	}

	$combat = '<option value=""></option>';
	$req = $db->query("SELECT id, nom FROM comp_combat ORDER BY nom ASC");
	while ($row = $db->read_assoc($req)) {
		$combat .= '<option value="'.$row['id'].'">'.$row['nom'].'</option>';
	}

	$sjeu = '<option value=""></option>';
	$req = $db->query("SELECT id, nom FROM sort_jeu ORDER BY nom ASC");
	while ($row = $db->read_assoc($req)) {
		$sjeu .= '<option value="'.$row['id'].'">'.$row['nom'].'</option>';
	}

	$scombat = '<option value=""></option>';
	$req = $db->query("SELECT id, nom FROM sort_combat ORDER BY nom ASC");
	while ($row = $db->read_assoc($req)) {
		$scombat .= '<option value="'.$row['id'].'">'.$row['nom'].'</option>';
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
			$icombat = "'".$_POST['comp_combat']."'";
			$ijeu = 'null';
			$isjeu = 'null';
			$iscombat = 'null';
			$ijoueur_id = 'null';
			$ijoueur_comp = 'null';
			$ijoueur_val = 'null';
		} elseif ($_POST['type'] == "jeu") {
			$icombat = 'null';
			$ijeu = "'".$_POST['comp_jeu']."'";
			$isjeu = 'null';
			$iscombat = 'null';
			$ijoueur_id = 'null';
			$ijoueur_comp = 'null';
			$ijoueur_val = 'null'; 
		} elseif ($_POST['type'] == "sjeu") {
			$icombat = 'null';
			$ijeu = 'null';
			$isjeu = "'".$_POST['sort_jeu']."'";
			$iscombat = 'null';
			$ijoueur_id = 'null';
			$ijoueur_comp = 'null';
			$ijoueur_val = 'null';
		} elseif ($_POST['type'] == "scombat") {
			$icombat = 'null';
			$ijeu = 'null';
			$isjeu = 'null';
			$iscombat = "'".$_POST['sort_combat']."'";
			$ijoueur_id = 'null';
			$ijoueur_comp = 'null';
			$ijoueur_val = 'null';
		} elseif ($_POST['type'] == "joueur") {
			$icombat = 'null';
			$ijeu = 'null';
			$isjeu = 'null';
			$iscombat = 'null';
			$ijoueur_id = 1;
			$ijoueur_comp = "'".$_POST['comp_joueur_competence']."'";
			$ijoueur_val = "'".$_POST['comp_joueur_valueadd']."'";
		}
		if (isset($_POST['resrve'])) {
			$iclasse = "'".implode(';', $_POST['resrve'])."'";
			$iclasse = mb_strtolower($iclasse);
		} else {
			$iclasse = 'null';
		}
		$cols = "nom, prix, comp_jeu, comp_combat, sort_jeu, sort_combat, comp_perso_id, comp_perso_competence, comp_perso_valueadd, classe_requis";
		$requete = "insert into grimoire ($cols) values ('$_POST[nom]', ".$_POST['prix'].", ".
			"$ijeu, $icombat, $isjeu, $iscombat, $ijoueur_id, $ijoueur_comp, $ijoueur_val, $iclasse)";

		echo $requete;
		$db->query($requete);
	}
	?>
<script type="text/javascript" language="javascript">
function hide_all() {
  var c = document.getElementById("tr_cbt");
  c.style.visibility = "hidden";
  var j = document.getElementById("tr_jeu");
  j.style.visibility = "hidden";
  var sc = document.getElementById("tr_scbt");
  sc.style.visibility = "hidden";
  var sj = document.getElementById("tr_sjeu");
  sj.style.visibility = "hidden";
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
function show_scombat() {
  hide_all();
  var sc = document.getElementById("tr_scbt");
  sc.style.visibility = "visible";
}
function show_sjeu() {
  hide_all();
  var sj = document.getElementById("tr_sjeu");
  sj.style.visibility = "visible";
}
function show_joueur() {
  hide_all();
  var p = document.getElementById("tr_joueur");
  p.style.visibility = "visible";
}

function name_comp(champ)
{
	document.getElementById('nom').value = 'Tome de ' + champ.options[champ.selectedIndex].text;
}

function name_sort(champ)
{
	document.getElementById('nom').value = 'Traité de ' + champ.options[champ.selectedIndex].text;
}
</script>
<form action="create_grimoire.php" method="post">
<table class="admin">
<tr>
	<td>
		Nom
	</td>
	<td>
		<input type="text" name="nom" id="nom" />
	</td>
	<td>
		Type
	</td>
	<td>
		<label onClick="javascript:show_combat();"><input type="radio" name="type" value="combat" />Compétence&nbsp;de&nbsp;combat</label><br />
		<label onClick="javascript:show_jeu();"><input type="radio" name="type" value="jeu" />Compétence&nbsp;hors&nbsp;combat</label><br />
		<label onClick="javascript:show_scombat();"><input type="radio" name="type" value="scombat" />Sort&nbsp;de&nbsp;combat</label><br />
		<label onClick="javascript:show_sjeu();"><input type="radio" name="type" value="sjeu" />Sort&nbsp;hors&nbsp;combat</label><br />
		<label onClick="javascript:show_joueur();"><input type="radio" name="type" value="joueur" />Compétence&nbsp;active</label>
	</td>
</tr>
<tr id="tr_cbt" style="visibility: hidden">
	<td>
		Compétence :
	</td>
	<td>
		<select name="comp_combat" onblur="name_comp(this);">
				<?= $combat ?>
		</select>
	</td>
</tr>
<tr id="tr_jeu" style="visibility: hidden">
	<td>
		Compétence :
	</td>
	<td>
		<select name="comp_jeu" onblur="name_comp(this);">
				<?= $jeu ?>
		</select>
	</td>
</tr>
<tr id="tr_scbt" style="visibility: hidden">
	<td>
		Sort :
	</td>
	<td>
		<select name="sort_combat" onblur="name_sort(this);">
				<?= $scombat ?>
		</select>
	</td>
</tr>
<tr id="tr_sjeu" style="visibility: hidden">
	<td>
		Sort :
	</td>
	<td>
		<select name="sort_jeu" onblur="name_sort(this);">
				<?= $sjeu ?>
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
		Prix
	</td>
	<td colspan="6">
		<input type="text" name="prix" id="prix" />
	<td>
	</td>
</tr>
<tr>
	<td>
		Réservé aux
	</td>
	<td colspan="6">
		<?= $reserve ?>
	<td>
	</td>
</tr>
</table>
<input type="submit" name="submit" value="Créer le grimoire" />
<form>
		<?php
}
?>