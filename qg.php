<?php // -*- tab-width:2; mode: php -*- 
if (file_exists('root.php'))
  include_once('root.php');


//Inclusion du haut du document html
include_once(root.'inc/fp.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$joueur->get_x().' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());
if ($R->is_raz())
{
	echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";
	exit (0);
}
echo "<fieldset>";
if($W_row['type'] == 1)
{
  ?>
  <legend><?php echo '<a href="ville.php" onclick="return envoiInfo(this.href, \'centre\')">';?><?php echo $R->get_nom();?></a> > <?php echo '<a href="qg.php" onclick="return envoiInfo(this.href, \'carte\')">';?> Quartier Général </a></legend>
<?php include_once(root.'ville_bas.php');?>	
	<div class="ville_test">
<?php
  if (array_key_exists('direction', $_GET))
  {
    ?>
	    <div style="text-align : center;"><a href="qg.php?direction=depot" onclick="return envoiInfo(this.href, 'carte')">Dépôt militaire</a>
	    </div>
	    <?php
    switch($_GET['direction'])
    {
      case 'prendre':
      if ($joueur->is_buff('debuff_rvr'))
      {
        echo '<h5>RvR impossible pendant la trêve</h5>';
        break;
      }
      if(array_key_exists('nbr', $_GET)) $nombre = $_GET['nbr'];
      else $nombre = 1;
      if($nombre > 0)
      {
        $i = 0;
        if ($nombre > ($G_place_inventaire - count($joueur->get_inventaire_slot_partie())))
        {
          $reste = $G_place_inventaire - count($joueur->get_inventaire_slot_partie()) ;
          if($reste != 0)
          {
             echo '<h5>Il ne vous reste que '.$reste.' places dans votre inventaire</h5>' ;
          }
          else
          {
             echo '<h5>Plus de place dans votre inventaire<br/></h5>';
          }
        }
        else
        {
          while($i < $nombre)
          {
            if(!array_key_exists('id', $_GET))
            {
              $requete = "SELECT objet_royaume.nom, depot_royaume.*, depot_royaume.id AS id_depot FROM depot_royaume, grade, objet_royaume WHERE depot_royaume.id_objet = objet_royaume.id AND id_royaume = ".$R->get_id()." AND id_objet = '".sSQL($_GET['id_objet'])."' AND objet_royaume.grade <= grade.rang  AND grade.id = ".$joueur->get_rang_royaume();
            }
            else
            {
              $requete = "SELECT objet_royaume.nom, depot_royaume.*, depot_royaume.id as id_depot FROM depot_royaume, grade, objet_royaume WHERE depot_royaume.id = ".sSQL($_GET['id'])." AND grade.id = ".$joueur->get_rang_royaume()." AND objet_royaume.grade <= grade.rang AND depot_royaume.id_objet = objet_royaume.id";
            }
            $req = $db->query($requete);
            $row = $db->read_array($req);
            if($db->num_rows > 0)
            {
              $requete2 = "DELETE FROM depot_royaume WHERE id = ".$row['id_depot'];
              if($db->query($requete2))
              {
                if($joueur->prend_objet('r'.$_GET['id_objet']))
                {
                  
                }
              }
              else
              {
                echo $G_erreur;
              }
            }
            $i++;
          }
          $joueur->sauver();
          $nom = $row['nom'] ;
          if($nombre > 1)
	  {
              $tab = array("Drapeau"=>"Drapeaux","Poste avancé"=>"Postes avancés", "Fortin"=>"Fortins", "Fort"=>"Forts", "Forteresse"=>"Forteresses", "Tour de guet"=>"Tours de guet", "Tour de garde"=>"Tours de garde", "Tour de mages"=>"Tours de mages", "Tour d archers"=>"Tours d'archers", "Bourgade"=>"Bourgades", "Palissade"=>"Palissades", "Mur"=>"Murs", "Muraille"=>"Murailles", "Grande muraille"=>"Grandes murailles", "Bélier"=>"Béliers", "Catapulte"=>"Catapultes", "Trébuchet"=>"Trébuchets", "Baliste"=>"Balistes", "Grand drapeau"=>"Grands drapeaux", "Étendard"=>"Étendards", "Grand étendard"=>"Grands étendards", "Petit drapeau"=>"Petits drapeaux") ; 
              if( in_array($row['nom'], array('Forteresse', 'Tour de guet', 'Tour de garde', 'Tour de mages', 'Tour d archers', 'Bourgade', 'Palissade', 'Muraille', 'Grande muraille', 'Catapulte', 'Baliste')) )
              {
                  echo '<h6>'.$nombre.' '.$tab[$row['nom']].' bien prises au dépôt du royaume</h6><br />';
              }
              else
              {
                  echo '<h6>'.$nombre.' '.$tab[$row['nom']].' bien pris au dépôt du royaume</h6><br />';
              }
                                               
          }
          else
          {
              if( in_array($row['nom'], array('Forteresse', 'Tour de guet', 'Tour de garde', 'Tour de mages', 'Tour d archers', 'Bourgade', 'Palissade', 'Muraille', 'Grande muraille', 'Catapulte', 'Baliste')) )
              {
                  echo '<h6>'.$nombre.' '.$row['nom'].' bien prise au dépôt du royaume</h6><br />';
              }
              else
              {
                  echo '<h6>'.$nombre.' '.$row['nom'].' bien pris au dépôt du royaume</h6><br />';
              }
          }
        }
      }
      break;
      case 'depot' :
   
    $requete = "SELECT o.nom, o.type, d.id_objet, d.id AS id_depot, COUNT(*) AS nbr_objet FROM depot_royaume as d, objet_royaume as o, grade as g WHERE d.id_objet = o.id AND g.id = ".$joueur->get_rang_royaume()." AND o.grade <= g.rang AND id_royaume = ".$R->get_id()." GROUP BY d.id_objet ORDER BY o.type, o.nom ASC";
    $type = "";
    ?>
    <table>
    <?php
        $req = $db->query($requete);
        while($row = $db->read_assoc($req))
        {
            if($type <> $row['type'])
            {
                $type = $row['type'];
                $tab = array("arme_de_siege"=>"Armes de siège","bourg"=>"Bourgades", "drapeau"=>"Drapeaux", "fort"=>"Forts", "mur"=>"Murs", "tour"=>"Tours") ; 
                echo "<tr><td colspan=2 align=center><b>";
                echo "<br />";
                echo $tab[$type] ;
                echo "</b></td></tr>";
            }
            ?>
                <tr>
                    <td>
                     <?php echo $row['nom']; ?> : <?php echo $row['nbr_objet']; ?>
                    </td>
                    <td>
                    <input type="text" id="nbr<?php echo $row['id_objet']; ?>" value="0" />
                     <a href="" onclick="return envoiInfo('qg.php?direction=prendre&amp;id_objet=<?php echo $row['id_objet']; ?>&amp;nbr=' + document.getElementById('nbr<?php echo $row['id_objet']; ?>').value, 'carte')">Prendre</a>
                    </td>
                </tr>
                <?php
        }
   
    ?>
   
    </table>
                <?php
      break;
    }
  }
  else
  {
    ?>
    <ul class="ville">
    <li>
        <a href="qg.php?direction=depot" onclick="return envoiInfo(this.href, 'carte')">Dépôt militaire</a>
    </li>
    </ul>
    </div>
    <?php
  }
}
echo "</fieldset>";
?>