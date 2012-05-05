<?PHP
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

  if(array_key_exists('perso', $_GET))
  {
    $_SESSION['nom'] = $_GET['perso'];
    $_SESSION['ID'] = $_GET['id'];
    echo 'Rehargement de la page en cours…<img src="image/pixel.gif" onLoad="document.location.reload();" />';
    exit;
  }
?>
<div class="titre">
	Changement de personnage
</div>
<div>
  <table>
    <tbody>
      <?php
      $requete = 'SELECT ID, nom, race, classe FROM perso WHERE id_joueur = '.$_SESSION['id_joueur'];
      $req = $db->query($requete);
      while( $row = $db->read_assoc($req) )
      {
        echo '<tr><td><a href="#" onClick="affichePopUp(\'changer_perso.php?perso='.$row['nom'].'&id='.$row['ID'].'\');"); return false;">'.$row['nom'].'</a></td><td>'.$row['race'].'</td><td>'.$row['classe'].'</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>