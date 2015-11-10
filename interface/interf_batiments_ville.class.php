<?php
/**
 * @file interf_batiments_ville.class.php
 * Interface de
 */ 

class interf_batiments_ville extends interf_bal_cont
{
	function __construct(&$royaume, $action)
	{
		global $db, $G_url, $G_tps_reparation, $G_tps_amelioration, $G_tps_reduction;
		parent::__construct('div', 'batiments_ville');
		$this->add( new interf_bal_smpl('h3', 'Liste des batiments de la ville :') );
		/// @todo passer à l'objet
    $requete = "SELECT *, construction_ville.id as id_const, construction_ville.hp as cur_hp, batiment_ville.hp as max_hp FROM construction_ville LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE id_royaume = ".$royaume->get_id();
    $req = $db->query($requete);
    $liste = $this->add( new interf_bal_cont('ul') );
    while($row = $db->read_assoc($req))
    {
    	$G_url->add('id', $row['id_const']);
    	$li = $liste->add( new interf_bal_cont('li', false, 'info_case') );
    	if( $action )
    	{
	    	if($row['statut'] != 'actif')
	    	{
					 $act = $li->add( new interf_lien_cont($G_url->get('action', 'reactif')) );
					 $act->add( new interf_bal_smpl('span', '', false, 'icone icone-reactif') );
					 $act->add( new interf_bal_smpl('span', $row['dette'], false, 'xsmall') );
					 $act->set_tooltip('Ré-activer ('.$row['dette'].' stars)');
				}
	    	else if( $row['cur_hp'] < $row['max_hp'] )
	    	{
					$temps = time() - $row['date'];
					if( $temps >= $G_tps_reparation )
					{
					 $cout = $row['cout'];// * ($row['max_hp'] - $row['cur_hp']) / $row['max_hp'];
					 $rep = $li->add( new interf_lien_cont($G_url->get('action', 'reparation')) );
					 $rep->add( new interf_bal_smpl('span', '', false, 'icone icone-architecture') );
					 $rep->add( new interf_bal_smpl('span', $cout, false, 'xsmall') );
					 $rep->set_tooltip('Réparer ('.$cout.' stars)');
					}
					else
						$li->add( new interf_bal_smpl('div', 'Vous devez attendre '.transform_min_temp($G_tps_reparation-$temps).' pour réparer', false, 'imposs') );
				}
				else
				{
					// amélioration
	        $requete = "SELECT * FROM batiment_ville WHERE level = ".$row['level']."+1 AND type = '".$row['type']."'";
	        $req2 = $db->query($requete);
	        if( $row2 = $db->read_assoc($req2) )
	        {
	        	$temps = time() - $row['date'];
	        	$temps_requis = ($row2['level'] - $row['level']) * $G_tps_amelioration;
	        	if( $temps >= $temps_requis )
	        	{
							$amel = $li->add( new interf_lien_cont($G_url->get('action', 'ameliore')) );
							$amel->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes-vous sûr de vouloir améliorer en '.$row2['nom'].'\');');
							$amel->add( new interf_bal_smpl('span', '', false, 'icone icone-plus') );
							$amel->add( new interf_bal_smpl('span', $row2['cout'].' ('.$row2['entretien'].')', false, 'xsmall') );
							$amel->set_tooltip('Améliorer en '.$row2['nom'].' ('.$row2['cout'].' stars, entretien : '.$row2['entretien'].' stars / jour)');
						}
						else
							$li->add( new interf_bal_smpl('div', 'Vous devez attendre '.transform_min_temp($temps_requis-$temps).' pour améliorer', false, 'imposs') );
					}
					// réduction
					$requete = "SELECT * FROM batiment_ville WHERE level = ".$row['level']."-1 AND type = '".$row['type']."' ORDER BY level DESC";
	  	    $req2 = $db->query($requete);
	  	    if( $row2 = $db->read_assoc($req2) )
	        {
		        $temps = time() - $row['date'];
		        $temps_requis = ($row['level'] - $row2['level']) * $G_tps_reduction;
		        if( $temps >= $temps_requis )
		        {
							$red = $li->add( new interf_lien_cont($G_url->get('action', 'reduit')) );
							$red->set_attribut('onclick', 'return verif_charger(this.href, \'Êtes-vous sûr de vouloir réduire en '.$row2['nom'].'\');');
							$red->add( new interf_bal_smpl('span', '', false, 'icone icone-moins') );
							$red->add( new interf_bal_smpl('span', '('.$row2['entretien'].')', false, 'xsmall') );
							$red->set_tooltip('Réduire en '.$row2['nom'].' (entretien : '.$row2['entretien'].' stars / jour)');
						}
						else
							$li->add( new interf_bal_smpl('div', 'Vous devez attendre '.transform_min_temp($temps_requis-$temps).' pour réduire', false, 'imposs') );
	        }
				}
			}
			$li->add( new interf_jauge_bulle('HP', $row['cur_hp'], $row['max_hp'], '%', 'hp', false, 'jauge_case') );
    	$li->add( new interf_bal_smpl('span', $row['nom'], false, 'nom') );
    	if($row['statut'] == 'actif')
    		$li->add( new interf_bal_smpl('span', 'entretien : '.$row['entretien'], false, 'small') );
    	else
    		$li->add( new interf_bal_smpl('span', 'inactif', false, 'small') );
		}
	}
}

?>