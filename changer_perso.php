<?PHP
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

  if(array_key_exists('perso', $_GET))
  {
    $_SESSION['nom'] = $_GET['perso'];
    $_SESSION['ID'] = $_GET['id'];
    //Mis à jour de la dernière connexion
		$requete = "UPDATE perso SET dernier_connexion = ".time().", statut = 'actif' WHERE ID = ".$_GET['id'];
		$db->query($requete);
    echo 'Rechargement de la page en cours…<img src="image/pixel.gif" onLoad="document.location.reload();" />';
    exit;
  }
  if(array_key_exists('info', $_GET))
    $info = $_GET['info'];
  else
    $info = false;
    
  $joueur = new joueur( $_SESSION['id_joueur'] );
?>
<div class="titre">
	Changement de personnage
</div>
<div>
  <table>
    <tbody>
      <?php
      $requete = 'SELECT ID, nom, race, classe FROM perso WHERE id_joueur = '.$_SESSION['id_joueur'].' ORDER BY id';
      $req = $db->query($requete);
      while( $row = $db->read_assoc($req) )
      {
        if($info)
          echo '<tr><td><a href="#" onClick="envoiInfo(\'changer_perso.php?perso='.urlencode($row['nom']).'&id='.$row['ID'].'\', \''.$info.'\'); return false;">'.$row['nom'].'</a></td><td>'.$row['race'].'</td><td>'.$row['classe'].'</td></tr>';
        else
          echo '<tr><td><a href="#" onClick="affichePopUp(\'changer_perso.php?perso='.urlencode($row['nom']).'&id='.$row['ID'].'\'); return false;">'.$row['nom'].'</a></td><td>'.$row['race'].'</td><td>'.$row['classe'].'</td></tr>';
      }
      ?>
    </tbody>
  </table>

  <?PHP
    if( $joueur->get_droits() & joueur::droit_pnj  )
    {
      echo '<br />Personnages non joueurs :<table><tbody>';
      $requete = 'SELECT ID, nom, race, classe FROM perso WHERE id_joueur = 0 ORDER BY id';
      $req = $db->query($requete);
      while( $row = $db->read_assoc($req) )
      {
        if($info)
          echo '<tr><td><a href="#" onClick="envoiInfo(\'changer_perso.php?perso='.urlencode($row['nom']).'&id='.$row['ID'].'\', \''.$info.'\'); return false;">'.$row['nom'].'</a></td><td>'.$row['race'].'</td><td>'.$row['classe'].'</td></tr>';
        else
          echo '<tr><td><a href="#" onClick="affichePopUp(\'changer_perso.php?perso='.urlencode($row['nom']).'&id='.$row['ID'].'\'); return false;">'.$row['nom'].'</a></td><td>'.$row['race'].'</td><td>'.$row['classe'].'</td></tr>';
      }
      echo '</table></tbody>';
    }
  ?>
</div>