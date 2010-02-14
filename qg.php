<?php // -*- tab-width:2 -*- 
if (file_exists('root.php'))
  include_once('root.php');


//Inclusion du haut du document html
include_once(root.'inc/fp.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT royaume, type FROM map WHERE ID =\''.sSQL($joueur->get_pos()).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());
echo "<fieldset>";
if($W_row['type'] == 1)
{
    ?>
    <legend><?php echo '<a href="ville.php" onclick="return envoiInfo(this.href, \'centre\')">';?><?php echo $R->get_nom();?></a> > <?php echo '<a href="qg.php" onclick="return envoiInfo(this.href, \'carte\')">';?> Quartier Général </a></legend>
		<?php include_once(root.'ville_bas.php');?>	
	<div class="ville_test">
    <?php
    if(array_key_exists('direction', $_GET))
    {
	    ?>
	    <div style="text-align : center;"><a href="qg.php?direction=depot" onclick="return envoiInfo(this.href, 'carte')">Dépôt militaire</a>
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
                	if ($nombre >= ($G_place_inventaire - count($joueur->get_inventaire_slot())))
                	{
                		echo 'Plus de place';
                	}
                	else
                	{
						while($i < $nombre)
						{
							if(!array_key_exists('id', $_GET))
							{
								$requete = "SELECT *, depot_royaume.id AS id_depot FROM depot_royaume LEFT JOIN objet_royaume ON depot_royaume.id_objet = objet_royaume.id WHERE id_royaume = ".$R->get_id()." AND id_objet=1";
							}
							else
							{
								$requete = "SELECT depot_royaume.*, depot_royaume.id as id_depot FROM depot_royaume, grade, objet_royaume WHERE depot_royaume.id = ".sSQL($_GET['id'])." AND grade.id = ".$joueur->get_rang_royaume()." AND objet_royaume.grade <= grade.rang AND depot_royaume.id_objet = objet_royaume.id";
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
										echo 'Objet bien pris au dépôt du royaume<br />';
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
                 $requete = "SELECT objet_royaume.*, depot_royaume.id_objet, depot_royaume.id AS id_depot FROM depot_royaume, objet_royaume WHERE depot_royaume.id_objet = objet_royaume.id AND id_objet = '1' AND id_royaume = ".$R->get_id();
                $req = $db->query($requete);

                ?>
                <tr>
                	<td>
                	 Drapeaux : <?php  echo $db->num_rows;?>
                	 <?php $row = $db->read_array($req);?>
  
                	</td>
                	<td>
                	<input type="text" id="nbr<?php echo $i; ?>" value="0" />
                	 <a href="" onclick="return envoiInfo('qg.php?direction=prendre&amp;id_objet=1&amp;nbr=' + document.getElementById('nbr<?php echo $i; ?>').value, 'carte')">Prendre</a>
                	</td>
                </tr>
                <?php
                $requete = "SELECT objet_royaume.*, depot_royaume.id_objet, depot_royaume.id AS id_depot FROM depot_royaume, objet_royaume, grade WHERE depot_royaume.id_objet = objet_royaume.id AND objet_royaume.grade <= grade.rang AND id_objet != '1' AND id_royaume = ".$R->get_id()." AND grade.id = ".$joueur->get_rang_royaume()." ORDER BY objet_royaume.nom ASC";
                $req = $db->query($requete);
    
                while($row = $db->read_assoc($req))
                {
                ?>
                <tr>
                    <td>
                    	
                        <?php echo $row['nom']; ?>
                    </td>
                    <td>
                        <a href="qg.php?direction=prendre&amp;id=<?php echo $row['id_depot']; ?>&amp;id_objet=<?php echo $row['id_objet']; ?>" onclick="return envoiInfo(this.href, 'carte')">Prendre</a>
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