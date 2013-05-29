<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');

include_once(root.'admin/menu_admin.php');

if( array_key_exists('action', $_GET) )
{
  switch( $_GET['action'] )
  {
  case 'mod_constr':
    $c = new construction($_GET['id']);
    $c->set_x($_POST['x']);
    $c->set_y($_POST['y']);
    $c->set_nom($_POST['nom']);
    $c->set_hp($_POST['hp']);
    $c->set_royaume($_POST['royaume']);
    $c->sauver();
    break;
  case 'mod_plac':
    $p = new placement($_GET['id']);
    $p->set_x($_POST['x']);
    $p->set_y($_POST['y']);
    $p->set_nom($_POST['nom']);
    $p->set_hp($_POST['hp']);
    $p->set_royaume($_POST['royaume']);
    $p->set_debut_placement($_POST['debut_placement']);
    $p->set_fin_placement($_POST['fin_placement']);
    $p->sauver();
    break;
  case 'nouv_constr':
    $c = new construction();
    $c->set_x($_POST['x']);
    $c->set_y($_POST['y']);
    $c->set_nom($_POST['nom']);
    $c->set_hp($_POST['hp']);
    $c->set_royaume($_POST['royaume']);
    $c->set_type('arme_de_siege');
    $c->set_id_batiment(52);
    $c->set_image('belier');
    $c->sauver();
    break;
  case 'nouv_plac':
    $p = new placement();
    $p->set_x($_POST['x']);
    $p->set_y($_POST['y']);
    $p->set_nom($_POST['nom']);
    $p->set_hp($_POST['hp']);
    $p->set_royaume($_POST['royaume']);
    $p->set_debut_placement($_POST['debut_placement']);
    $p->set_fin_placement($_POST['fin_placement']);
    $p->set_type('arme_de_siege');
    $p->set_id_batiment(52);
    $p->sauver();
    break;
  }
}

?>
  <h3>Uniquement pour les bélier d'entrainement pour l'instant</h3>
  <h4>Contruction</h4>
  <table>
    <tbody>
      <tr><th>x</th><th>y</th><th>nom</th><th>hp</th><th>royaume</th><th>&nbsp;</th></tr>
<?php
  $roy = array();
  $req = $db->query('select id, race from royaume');
  while($row = $db->read_object($req))
  {
    $roy[$row->id] = $row->race;
  }
  $roy[0] = 'neutre';
  $req = $db->query('select * from construction where id_batiment=52');
  while($row = $db->read_object($req))
  {
    echo '<tr><form id="plac_'.$row->id.'" method="post" action="edit_constr.php?action=mod_constr&id='.$row->id.'">';
    echo '<td><input type="text" value="'.$row->x.'" name="x" size="3"></td>';
    echo '<td><input type="text" value="'.$row->y.'" name="y" size="3"></td>';
    echo '<td><input type="text" value="'.$row->nom.'" name="nom" size="50"></td>';
    echo '<td><input type="text" value="'.$row->hp.'" name="hp" size="6"></td>';
    echo '<td><select name="royaume">';
    foreach($roy as $id=>$r)
    {
      echo '<option value="'.$id.'"'.($row->royaume==$id?' selected="selected"':'').'>'.$r.'</option>';
    }
    echo '</select></td>';
    echo "<td><input type=\"submit\" value=\"Update\"/></td>";
    echo '</form></tr>';
  }
?>
    </tbody>
  </table>
  Nouveau :
  <form method="post" action="edit_constr.php?action=nouv_constr">
  	 Nom : <input type="text" name="nom" value="Bélier d'entrainement" size="50"/><br/>
  	 HP : <input type="text" name="hp" value="100000" size="6"/>
  	 X : <input type="text" name="x" size="3"/>
  	 Y : <input type="text" name="y" size="3"/><br/>
<?php
    echo 'Royaume : <select name="royaume">';
    foreach($roy as $id=>$r)
    {
      echo '<option value="'.$id.'">'.$r.'</option>';
    }
    echo '</select>';
?>
  	 <input type="submit" value="Créer"/>
  </form>
  <br />
  <h4>Placement</h4>
  <table>
    <tbody>
      <tr><th>x</th><th>y</th><th>nom</th><th>hp</th><th>royaume</th><th>date début</th><th>date fin</th><th>&nbsp;</th></tr>
<?php
  $req = $db->query('select * from placement where id_batiment=52');
  while($row = $db->read_object($req))
  {
    echo '<tr><form id="plac_'.$row->id.'" method="post" action="edit_constr.php?action=mod_plac&id='.$row->id.'">';
    echo '<td><input type="text" value="'.$row->x.'" name="x" size="3"></td>';
    echo '<td><input type="text" value="'.$row->y.'" name="y" size="3"></td>';
    echo '<td><input type="text" value="'.$row->nom.'" name="nom" size="50"></td>';
    echo '<td><input type="text" value="'.$row->hp.'" name="hp" size="6"></td>';
    echo '<td><select name="royaume">';
    foreach($roy as $id=>$r)
    {
      echo '<option value="'.$id.'"'.($row->royaume==$id?' selected="selected"':'').'>'.$r.'</option>';
    }
    echo '</select></td>';
    echo '<td><input type="text" value="'.$row->debut_placement.'" name="debut_placement" size="9"></td>';
    echo '<td><input type="text" value="'.$row->fin_placement.'" name="fin_placement" size="9"></td>';
    echo "<td><input type=\"submit\" value=\"Update\"/></td>";
    echo '</form></tr>';
  }
?>
    </tbody>
  </table>
  Nouveau :
  <form method="post" action="edit_constr.php?action=nouv_plac">
  	 Nom : <input type="text" name="nom" value="Contruction d'entrainement" size="50"/><br/>
  	 HP : <input type="text" name="hp" value="100000" size="6"/>
  	 X : <input type="text" name="x" size="3"/>
  	 Y : <input type="text" name="y" size="3"/><br/>
  	 date début : <input type="text" name="debut_placement" value="1365579505" size="9"/>
  	 date fin : <input type="text" name="fin_placement" value="1965579505" size="9"/><br/>
<?php
    echo 'Royaume : <select name="royaume">';
    foreach($roy as $id=>$r)
    {
      echo '<option value="'.$id.'">'.$r.'</option>';
    }
    echo '</select>';
?>
  	 <input type="submit" value="Créer"/>
  </form>
<?php
include_once(root.'admin/admin_bas.php');
?>
