<?php
if (file_exists('root.php'))
  include_once('root.php');


//Inclusion du haut du document html
include_once(root.'inc/fp.php');

$joueur = new perso($_SESSION['ID']);

$W_case = $_GET['poscase'];
$W_req = $db->query('SELECT royaume FROM map WHERE ID =\''.sSQL($W_case).'\'');
$W_row = $db->read_array($W_req);
$royaume = new royaume($W_row['royaume']);

$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$_SESSION['position'] = convert_in_pos($joueur->get_x(), $joueur->get_y());

$W_distance = detection_distance($W_case, $_SESSION["position"]);
if($W_distance == 0)
{
    ?>
    <h2 class="ville_titre"><?php echo '<a href="ville.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'centre\')">';?><?php echo $royaume->get_nom();?></a> - <?php echo '<a href="qg.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'carte\')">';?> Quartier Général </a></h2>
		<?php include_once(root.'ville_bas.php');?>	
	<div class="ville_test">
    <?php
    if(array_key_exists('direction', $_GET))
    {
	    ?>
	    <div style="text-align : center;"><a href="qg.php?poscase=<?php echo $W_case; ?>&amp;direction=depot" onclick="return envoiInfo(this.href, 'carte')">Dépôt militaire</a>
	    </div>
	    <?php
        switch($_GET['direction'])
        {
            case 'prendre' :
                if(array_key_exists('nbr', $_GET)) $nombre = $_GET['nbr'];
                else $nombre = 1;
            	if($nombre > 0)
            	{
                	$i = 0;
                	if ($nombre > ($G_place_inventaire - count($joueur->get_inventaire_slot())))
                	{
                		echo 'Plus de place';
                	}
                	else
                	{
						while($i < $nombre)
						{
							if(!array_key_exists('id', $_GET))
							{
								$requete = "SELECT *, depot_royaume.id AS id_depot FROM depot_royaume LEFT JOIN objet_royaume ON depot_royaume.id_objet = objet_royaume.id WHERE grade <= ".$joueur->get_rang_royaume()." AND id_royaume = ".$royaume->get_id();
							}
							else
							{
								$requete = "SELECT *, id as id_depot FROM depot_royaume WHERE id = ".sSQL($_GET['id']);
							}
							$req = $db->query($requete);
							$row = $db->read_array($req);
							if($db->num_rows > 0)
							{
								$requete2 = "DELETE FROM depot_royaume WHERE id = ".$row['id_depot'];
								if($db->query($requete2))
								{
									if(prend_objet('r'.$_GET['id_objet'], $joueur))
									{
										echo 'Objet bien pris au dépôt du royaume<br />';
										$joueur = recupperso($joueur->get_id());
									}
								}
								else
								{
									echo $G_erreur;
								}
							}
							$i++;
						}
                	}
            	}
            break;
            case 'depot' :
                ?>
                <table>
                <tr>
                    <td>
                        Nom
                    </td>
                    <td>
                        Prendre
                    </td>
                </tr>
                <?php
                 $requete = "SELECT *, depot_royaume.id AS id_depot FROM depot_royaume LEFT JOIN objet_royaume ON depot_royaume.id_objet = objet_royaume.id WHERE grade <= ".$joueur->get_rang_royaume()." AND id_objet = '1' AND id_royaume = ".$royaume->get_id();
                $req = $db->query($requete);

                ?>
                <tr>
                	<td>
                	 Drapeaux : <?php  echo $db->num_rows;?>
                	 <?php $row = $db->read_array($req);?>
  
                	</td>
                	<td>
                	<input type="text" id="nbr<?php echo $i; ?>" value="0" />
                	 <a href="" onclick="return envoiInfo('qg.php?poscase=<?php echo $W_case; ?>&amp;direction=prendre&amp;id_objet=1&amp;nbr=' + document.getElementById('nbr<?php echo $i; ?>').value, 'carte')">Prendre</a>
                	</td>
                </tr>
                <?
                
                
                $requete = "SELECT *, depot_royaume.id AS id_depot FROM depot_royaume LEFT JOIN objet_royaume ON depot_royaume.id_objet = objet_royaume.id WHERE grade <= ".$joueur->get_rang_royaume()." AND id_objet != '1' AND id_royaume = ".$royaume->get_id()." ORDER BY nom ASC";
                $req = $db->query($requete);
    
                while($row = $db->read_assoc($req))
                {
                ?>
                <tr>
                    <td>
                    	
                        <?php echo $row['nom']; ?>
                    </td>
                    <td>
                        <a href="qg.php?poscase=<?php echo $W_case; ?>&amp;direction=prendre&amp;id=<?php echo $row['id_depot']; ?>&amp;id_objet=<?php echo $row['id_objet']; ?>" onclick="return envoiInfo(this.href, 'carte')">Prendre</a>
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
        <a href="qg.php?poscase=<?php echo $W_case; ?>&amp;direction=depot" onclick="return envoiInfo(this.href, 'carte')">Dépôt militaire</a>
    </li>
    </ul>
    </div>
    <?php
    }
}
?>