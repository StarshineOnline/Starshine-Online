<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');



/*********************************************************************/
/********************** TERRAINS DE PERSONNAGES **********************/
/*********************************************************************/

// On crée les terrains pour les persos qui ont gagné une enchère
$listeIdVenteTerrainGagne = array();
$query = "
	SELECT id, id_joueur, prix
	FROM vente_terrain
	WHERE id_joueur != 0 AND date_fin <= ".time()."
";
$result = $db->query($query);
while($venteTerrain = $db->read_assoc($result))
{
	$listeIdVenteTerrainGagne[] = $venteTerrain['id'];
	$terrain = new terrain(0, $venteTerrain['id_joueur'], 2);
	$terrain->sauver();
	$mail .= "Le joueur d'id ".$venteTerrain['id_joueur']." gagne un terrain pour ".$venteTerrain['prix']." stars.\n";
}
// On supprime les enchères gagnées par les persos
if(count($listeIdVenteTerrainGagne) > 0)
{
	$implode_ids = implode(', ', $listeIdVenteTerrainGagne);
	// La requête ne supprime l'enchère que si le terrain a bien été créé dans la BDD
	$query = "
		DELETE vt
		FROM vente_terrain vt
		INNER JOIN terrain t ON vt.id_joueur = t.id_joueur
		WHERE vt.id IN (".$implode_ids.")
	";
	$db->query($query);
}
// On supprime les enchères terminées que personne n'a gagnées
$query = "
	DELETE FROM vente_terrain
	WHERE id_joueur = 0 AND date_fin <= ".time()."
";
$db->query($query);



/**********************************************************************************/
/********************** BOURSE AUX RESSOURCES INTER-ROYAUMES **********************/
/**********************************************************************************/

$bourse = new bourse(0);
$bourse->get_encheres('DESC','actif = 1');
foreach($bourse->encheres as $enchere)
{
  if( $enchere->id_royaume_acheteur )
  {
    $id_r = array();
    for($ids=$enchere->id_royaume_acheteur, $id=1; $ids; $ids >>=1,$id++)
    {
      if( $ids & 1 )
        $id_r[] = $id;
    }
    $requete = 'update royaume set star = star + '.$enchere->prix.' where id = '.$enchere->id_royaume;
    $req = $db->query($requete);
    $n = count($id_r);
    if( $n > 1 )
    {
      $recup = floor($enchere->prix * (1 - 1/$n) );
      $ress = floor($enchere->nombre / $n);
      $requete = 'update royaume set star = star + '.$recup.', '.$enchere->ressource.' = '.$enchere->ressource.' + '.$ress.' where id in ('.implode(',', $id_r).')';
      $req = $db->query($requete);
    }
    else
    {
      $requete = 'update royaume set '.$enchere->ressource.' = '.$enchere->ressource.' + '.$enchere->nombre.' where id ='.$id_r[0];
      $req = $db->query($requete);
    }
    $enchere->actif = 0;
    $enchere->fin_vente = date("Y-m-d");
  }
  else
  {
    $ratio = $enchere->prix / $enchere->nombre;
    $red = 1 / (1.15+sqrt($ratio)/10);
    $enchere->prix = min($enchere->prix * $red, $enchere->prix-10);
    // prix de rachat par le jeu
    $requete = 'select sum('.$enchere->ressource.') as total from royaume';
    $req = $db->query($requete);
    $row = $db->read_array($req);
    $total = $row['total'];
    $requete = 'select sum(nombre) as total from bourse_royaume where actif = 1 and ressource = "'.$enchere->ressource.'"';
    $req = $db->query($requete);
    $row = $db->read_array($req);
    $total += $row['total'];
    $rachat = $enchere->nombre * .2 / sqrt(1 + $total/100000);
    // rachat par le jeu
    $max = 500 * pow(2, 1 - $enchere->prix/$rachat);
    if( rand(1, 500) <= $max )
    {
      $requete = 'update royaume set star = star + '.$rachat.' where id = '.$enchere->id_royaume;
      $req = $db->query($requete);
      $enchere->actif = 0;
      $enchere->fin_vente = date("Y-m-d");
      $requete = 'update royaume set '.$enchere->ressource.' = '.$enchere->ressource.' + '.$enchere->nombre.' where id = 0';
      $req = $db->query($requete);
    }
  }
  $enchere->sauver();
}

?>