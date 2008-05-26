<?php

//Inclusion du haut du document html
include('inc/fp.php');

$joueur = recupperso($_SESSION['ID']);

$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);

check_perso($joueur);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);

$W_distance = detection_distance($W_case, $_SESSION["position"]);
if($W_distance == 0)
{
    ?>
    <h2 class="ville_titre"><?php echo '<a href="javascript:envoiInfo(\'ville.php?poscase='.$W_case.'\', \'centre\')">';?><?php echo $R['nom'];?></a> - <?php echo '<a href="javascript:envoiInfo(\'qg.php?poscase='.$W_case.'\', \'carte\')">';?> Quartier Général </a></h2>
		<?php include('ville_bas.php');?>	
	<div class="ville_test">
    <?php
    if(array_key_exists('direction', $_GET))
    {
	    ?>
	    <div style="text-align : center;"><a href="javascript:envoiInfo('qg.php?poscase=<?php echo $W_case; ?>&amp;direction=depot', 'carte')">Dépôt militaire</a>
	    <?php
	    if($joueur['rang_royaume'] == 6)
	    {
		    ?>
		    - <a href="javascript:envoiInfo('qg.php?poscase=<?php echo $W_case; ?>&amp;direction=boutique', 'carte')">Boutique militaire</a>
		    </div><br />
    		<strong>Stars du royaume</strong> : <?php echo $R['star']; ?><br />
		    <strong>Taux de taxe</strong> : <?php echo $R['taxe_base']; ?>%<br />
		    <?php
	    }
	    else echo '</div>';
        switch($_GET['direction'])
        {
            case 'achat' :
                $requete = "SELECT * FROM objet_royaume WHERE id = ".sSQL($_GET['id']);
                $nombre = $_GET['nbr'];
                $req = $db->query($requete);
                $row = $db->read_assoc($req);
                $check = true;
            	//Si c'est pour une bourgade on vérifie combien il y en a déjà
            	if($row['type'] == 'bourg')
            	{
	            	$nb_bourg = nb_bourg($R['ID']);
	            	$nb_case = nb_case($R['ID']);
	            	if(($nb_bourg + $nombre - 1) >= ceil($nb_case / 250)) $check = false;
            	}
                if($R['star'] >= ($row['prix'] * $nombre) && $check)
                {
	                $i = 0;
	                while($i < $nombre)
	                {
                    	//Achat
                    	$requete = "INSERT INTO depot_royaume VALUES ('', ".$row['id'].", ".$R['ID'].")";
                    	$db->query($requete);
                    	//On rajoute un bourg au compteur
		            	if($row['type'] == 'bourg')
        		    	{
	        		    	$requete = "UPDATE royaume SET bourg = bourg + 1 WHERE ID = ".$R['ID'];
	        		    	$db->query($requete);
        		    	}
                    	//On enlève les stars au royaume
                    	$requete = "UPDATE royaume SET star = star - ".$row['prix']." WHERE ID = ".$R['ID'];
                    	if($db->query($requete))
                    	{
                    	    echo $row['nom'].' bien acheté.<br />';
                    	}
                    	$i++;
                	}
                }
                elseif(!$check)
                {
                    echo 'Il y a déjà trop de bourg sur votre royaume.<br />
                    Actuellement : '.$nb_bourg.'<br />
                    Maximum : '.ceil($nb_case / 250);
                }
                else
                {
                    echo 'Le royaume n\'a pas assez de stars';
                }
           	    echo '<a href="javascript:envoiInfo(\'qg.php?poscase='.$W_case.'&amp;direction=boutique\', \'carte\')">Retour à la boutique militaire</a>';
            break;
            case 'boutique' :
                ?>
                <table>
                <tr>
                    <td>
                        Nom
                    </td>
                    <td>
                        Prix
                    </td>
                    <td>
                    	Nombre
                    </td>
                    <td>
                        Achat
                    </td>
                </tr>
                <?php
                $requete = "SELECT * FROM objet_royaume";
                $req = $db->query($requete);
                $i = 0;
                while($row = $db->read_assoc($req))
                {
                ?>
                <tr>
                    <td>
                        <?php echo $row['nom']; ?>
                    </td>
                    <td>
                        <?php echo $row['prix']; ?>
                    </td>
                    <td>
                    	<input type="text" id="nbr<?php echo $i; ?>" value="1" />
                    </td>
                    <td>
                        <a href="javascript:envoiInfo('qg.php?poscase=<?php echo $W_case; ?>&amp;direction=achat&amp;id=<?php echo $row['id']; ?>&amp;nbr=' + document.getElementById('nbr<?php echo $i; ?>').value, 'carte')">Acheter</a>
                    </td>
                </tr>
                <?php
                	$i++;
                }
                ?>
                </table>
                <?php
            break;
            case 'prendre' :
                $nombre = $_GET['nbr'];
                $i = 0;
                if ($nombre > ($G_place_inventaire - count($joueur['inventaire_slot'])))
                {
                	echo 'Plus de place';
                }
                else
                {
					while($i < $nombre)
					{
						if(prend_objet('r'.$_GET['id_objet'], $joueur))
						{
							$requete = "SELECT *, depot_royaume.id AS id_depot FROM depot_royaume LEFT JOIN objet_royaume ON depot_royaume.id_objet = objet_royaume.id WHERE grade <= ".$joueur['rang_grade']." AND id_royaume = ".$R['ID'];
							$req = $db->query($requete);
							$row = $db->read_array($req);
							$requete2 = "DELETE FROM depot_royaume WHERE id = ".$row['id_depot'];
							$db->query($requete2);
							echo 'Objet bien pris au dépôt du royaume<br />';
							$joueur = recupperso($joueur['ID']);
						}
						else
						{
							echo $G_erreur;
						}
						$i++;
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
                 $requete = "SELECT *, depot_royaume.id AS id_depot FROM depot_royaume LEFT JOIN objet_royaume ON depot_royaume.id_objet = objet_royaume.id WHERE grade <= ".$joueur['rang_grade']." AND id_objet = '1' AND id_royaume = ".$R['ID'];
                $req = $db->query($requete);

                ?>
                <tr>
                	<td>
                	 Drapeaux : <?php  echo $db->num_rows;?>
                	 <?php $row = $db->read_array($req);?>
  
                	</td>
                	<td>
                	<input type="text" id="nbr<?php echo $i; ?>" value="0" />
                	 <a href="javascript:envoiInfo('qg.php?poscase=<?php echo $W_case; ?>&amp;direction=prendre&amp;id_objet=1&amp;nbr=' + document.getElementById('nbr<?php echo $i; ?>').value, 'carte')">Prendre</a>
                	</td>
                </tr>
                <?
                
                
                $requete = "SELECT *, depot_royaume.id AS id_depot FROM depot_royaume LEFT JOIN objet_royaume ON depot_royaume.id_objet = objet_royaume.id WHERE grade <= ".$joueur['rang_grade']." AND id_objet != '1' AND id_royaume = ".$R['ID'];
                $req = $db->query($requete);
    
                while($row = $db->read_assoc($req))
                {
                ?>
                <tr>
                    <td>
                    	
                        <?php echo $row['nom']; ?>
                    </td>
                    <td>
                        <a href="javascript:envoiInfo('qg.php?poscase=<?php echo $W_case; ?>&amp;direction=prendre&amp;id=<?php echo $row['id_depot']; ?>&amp;id_objet=<?php echo $row['id_objet']; ?>', 'carte')">Prendre</a>
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
    <?php
        if($joueur['rang_royaume'] == 6)
        {
    ?>
    <li>
        <a href="javascript:envoiInfo('qg.php?poscase=<?php echo $W_case; ?>&amp;direction=boutique', 'carte')">Boutique militaire</a>
    </li>
    <?php
        }
    ?>
    <li>
        <a href="javascript:envoiInfo('qg.php?poscase=<?php echo $W_case; ?>&amp;direction=depot', 'carte')">Dépôt militaire</a>
    </li>
    </ul>
    </div>
    <?php
    }
}
?>