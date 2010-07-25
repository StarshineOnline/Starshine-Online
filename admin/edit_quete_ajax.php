<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
$ajax = true;
include_once(root.'inc/fp.php');
include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');

if (array_key_exists('action', $_POST) && $_POST['action'] == 'post')
{
	$objectifs = array();
	for ($i = 0; $i < count($_POST['cible']); $i++) {
		$objectifs[$i]->cible = $_POST['cible'][$i];
		$objectifs[$i]->nombre = $_POST['nombre'][$i];
		$objectifs[$i]->requis = $_POST['requis'][$i];
	}
	$strobjectifs = serialize($objectifs);

	$requete = "update quete set nom = '".sSQL($_POST['nom']).
		"', description = '".sSQL($_POST['description'])."'";

	$requete .= ', exp = \''.sSQL($_POST['exp']).'\'';
	$requete .= ', honneur = \''.sSQL($_POST['honneur']).'\'';
	$requete .= ', star = \''.sSQL($_POST['star']).'\'';
	$requete .= ', niveau_requis = \''.sSQL($_POST['niveau_requis']).'\'';
	$requete .= ', quete_requis = \''.sSQL($_POST['quete_requis']).'\'';
	$requete .= ', star_royaume = \''.sSQL($_POST['star_royaume']).'\'';
	$requete .= ', lvl_joueur = \''.sSQL($_POST['lvl_joueur']).'\'';
	$requete .= ', mode = \''.sSQL($_POST['mode']).'\'';
	$requete .= ', achat = \''.sSQL($_POST['achat']).'\'';
	$requete .= ', fournisseur = \''.sSQL($_POST['fournisseur']).'\'';

	if (count($objectifs))
		$requete .= ', objectif = \''.$strobjectifs.'\'';

	$requete .= ' where id = '.sSQL($_REQUEST['id'], SSQL_INTEGER);
	$db->query($requete);
	echo 'Sauvegarde ok';
}

$req = $db->query("select * from quete where id = ".sSQL($_REQUEST['id'], SSQL_INTEGER));
$quete = $db->read_object($req);

echo '<form id="editqueteform"><table><tbody>';
echo '<input type="hidden" name="id" value="'.intval($_REQUEST['id']).'" />';
echo '<input type="hidden" name="action" value="post" />';
print_key_value_form_row('nom', $quete->nom, null);
print_key_value_form_row('description', $quete->description, 'textarea', null, null, true, true, 3);
print_key_value_form_row('exp', $quete->exp, null, null, null, true, false);
print_key_value_form_row('honneur', $quete->honneur, null, null, null, false);
print_key_value_form_row('star', $quete->star, null, null, null, true, false);
print_key_value_form_row('niveau_requis', $quete->niveau_requis, null, null, null, false);
print_key_value_form_row('honneur_requis', $quete->honneur_requis, null, null, null, true, false);
print_key_value_form_row('quete_requis', $quete->quete_requis, null, null, null, false);
print_key_value_form_row('star_royaume', $quete->star_royaume, null, null, null, true, false);
print_key_value_form_row('lvl_joueur', $quete->lvl_joueur, null, null, null, false);
print_key_value_form_row('repete', $quete->repete, 'combo', null,
												 array('oui' => 'y', 'non' => 'n'), true, false);
print_key_value_form_row('mode', $quete->mode, 'combo', null,
												 array('Groupe' => 'g', 'Solo' => 's'), false);
print_key_value_form_row('achat', $quete->achat, 'combo', null,
												 array('oui' => 'oui', 'non' => 'non'), true, false);
print_key_value_form_row('fournisseur', $quete->fournisseur, 'combo', null, 
												 array('Aucun' => '',
															 'École de combat' => 'ecole_combat',
															 'Magasin' => 'magasin', 'Taverne' => 'taverne'),
												 false);

echo '<tr><td>Objectifs&nbsp:<br/><a href="javascript:AddObj()">Ajouter</a>';
echo '</td><td colspan="3"><table id="objtbl" style="border: 1px black solid; width: 100%">';

foreach (unserialize($quete->objectif) as $num => $obj) {
	echo '<tr id="p_'.$num.
		'"><td>Cible&nbsp;: <input type="text" name="cible[]" value="'.
		$obj->cible.'" size="4" onclick="selCible(this)"/></td><td>Nombre&nbsp;: '.
		'<input type="text" size="4" name="nombre[]" value="'.$obj->nombre.
		'" /></td><td>Requis&nbsp;: <input type="text" size="4" value="'.
		$obj->requis.'" name="requis[]" /></td>';
	echo '<td><a href="javascript:DelObj(\'p_'.$num.'\')">Effacer</a></td></tr>';
}

echo '</table></td></tr>';

echo '<tr><td></td><td><input type="button" value="valider" onclick="doValidEditQuest()" /></td></tr>';
echo '</tbody></table></form>';

?>
<script type="text/javascript">
function doValidEditQuest() {
  $.post('edit_quete_ajax.php', $('#editqueteform').serialize(), function(d){
	$('#showquest').html(d); });
}

var baseid = 1;

function AddObj() {
	var myid = 'auto_' + baseid;
	baseid = baseid + 1;
	$('#objtbl').append('<tr id="' + myid + '"><td>Cible&nbsp;: ' +
											'<input type="text" name="cible[]" size="4" '+
											'onclick="selCible(this)"/></td><td>Nombre&nbsp;: ' +
											'<input type="text" size="4" name="nombre[]" /></td>' +
											'<td>Requis&nbsp;: <input type="text" size="4" ' + 
											'name="requis[]" /></td><td>' + 
											'<a href="javascript:DelObj(\'' +	myid +
											'\')">Effacer</a></td></tr>');
}

function DelObj(obj) {
	$('#' + obj).remove();
}
</script>
