<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

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
      $ress = floor($enchere->ressource / $n);
      $requete = 'update royaume set star = star + '.$recup.', '.$enchere->ressource.' = '.$enchere->ressource.' + '.$ress.' where id in ('.implode(',', $id_r).')';
      $req = $db->query($requete);
    }
    else
    {
      $requete = 'update royaume set '.$enchere->ressource.' = '.$enchere->ressource.' + '.$enchere->ressource.' where id ='.$id_r[0];
      $req = $db->query($requete);
    }
    $enchere->actif = 0;
    $enchere->fin_vente = date("Y-m-d");
  }
  else
  {
    $enchere->prix = min($enchere->prix * .8, $enchere->prix-10);
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
    if( $enchere->prix < $rachat )
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