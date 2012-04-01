<?php
/*
	r = round
	c = competence
	s = sort
	e = esquive
	m = manque la cible avec sort
	l = lancement sort raté
	~12 = degats
	~a = anticipation
	; = fin d'un round
	, = changement de personne ou effets
	n = s'approche
	cp = paralysé
	ce = etourdi
	ef = effet
*/

class combat
{
	public $id;
	public $attaquant;
	public $defenseur;
	public $combat;
	public $id_journal;
	
	/**	
		*	Constructeur permettant la création d'un combat
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-combat() qui construit un etat "vide".
		*		-combat($id) qui va chercher l'etat dont l'id est $id
		*		-combat($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $attaquant = 0, $defenseur = 0, $combat = '', $id_journal = 0)
	{
		global $db;
		//Verification du attaquantbre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT attaquant, defenseur, combat, id_journal FROM combats WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->attaquant, $this->defenseur, $this->combat, $this->id_journal) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->attaquant = $id['attaquant'];
			$this->defenseur = $id['defenseur'];
			$this->id_journal = $id['id_journal'];
			$this->combat = $id['combat'];
		}
		else
		{
			$this->attaquant = $attaquant;
			$this->defenseur = $defenseur;
			$this->id_journal = $id_journal;
			$this->combat = $combat;
			$this->id = $id;
		}		
	}
	
	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE combats SET ';
			$requete .= 'attaquant = "'.$this->attaquant.'", defenseur = "'.$this->defenseur.'", id_journal = '.$this->id_journal.', combat = "'.$this->combat.'"';
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO combats (attaquant, defenseur, combat, id_journal) VALUES(';
			$requete .= '"'.$this->attaquant.'", "'.$this->defenseur.'", "'.$this->combat.'", '.$this->id_journal.')';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id) = $db->last_insert_id();
		}
	}
	
	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM combats WHERE id = '.$this->id;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id;
	}
	
	function get_combat()
	{
		return $this->combat;
	}
	
	function afficher_combat()
	{
	global $db;
		if ($this->combat != NULL)
		{
			$attaquant = new perso($this->attaquant);
			$defenseur = new perso($this->defenseur);
			$logcombat = preg_replace("#r[0-9]+:#", "", $this->combat);
			$rounds = explode(';', $logcombat);
			unset($combat);
			for($i = 0; $i < count($rounds); $i++)
			{
				$each_attaque = explode(',', $rounds[$i] );
				$combat[$i+1]['attaquant'] = $each_attaque[0];
				$combat[$i+1]['defenseur'] = $each_attaque[1];
				$combat[$i+1]['effects_attaquant'] = $each_attaque[2];
				$combat[$i+1]['effects_defenseur'] = $each_attaque[3];
			}

			echo '<fieldset>
			<legend>Combat VS '.$defenseur->get_nom().' </legend>';
			$round = 1;
			while($round < (count($combat)+1))
			{		
				if ($mode == 'attaquant') $mode = 'defenseur';
				else ($mode = 'attaquant');
				
				if($mode == 'attaquant')
				{
					echo '
					<table style="width : 100%;">
						<tr>
							<td style="vertical-align : top; width : 20%;">
								<h3 style="margin-top : 3px;">Round '.$round.'</h3>
							</td>
							<td>';
				}
				echo '<div class="combat">';
				
				preg_match("#([a-z])([0-9a-z]*)(!)?(~([0-9aelm]+))?#i", $combat[$round][$mode], $attaque);
				/*
				$attaque[1] => c // Si c'est une compétence, un sort ...
				$attaque[2] => 0 // l'id de la compétence ou du sort
				$attaque[3] => ! // critiques
				$attaque[5] => e // les degats ou esquive ou anticipation
				*/
				if($attaque[1] == "c") // Une compétence
				{
						if($attaque[2] != 0 AND is_numeric($attaque[2]))
						{
							$requete = "SELECT * FROM comp_combat WHERE id = ".$attaque[2];
							$req = $db->query($requete);
							$row = $db->read_assoc($req);
							
							switch($row['type'])
							{
								case 'tir_precis' :
								case 'oeil_faucon' :
								case 'coup_puissant' :
								case 'coup_violent' :
								case 'coup_mortel' :
								case 'coup_sournois' :
								case 'attaque_vicieuse' :
								case 'tir_puissant' :
								case 'fleche_magnetique' :
								case 'fleche_sable' :
								case 'fleche_rapide' :
								case 'fleche_sanglante' :
								case 'frappe_derniere_chance' :
								case 'feinte' :
								case 'attaque_cote' :
								case 'attaque_brutale' :
								case 'attaque_rapide' :
									echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> utilise '.$row['nom'].'<br />';
								break;
								case 'fleche_debilitante' :
									echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> utilise '.$row['nom'].'<br />';
								break;
								case 'fleche_poison' :
									echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> utilise '.$row['nom'].'<br />';
								break;
								case 'berzeker' :
									echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> passe en mode '.$row['nom'].' !<br />';
								break;
								case 'tir_vise' :
									echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> se concentre pour viser !<br />';
								break;
								case 'fleche_etourdissante' :
									echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> utilise une flêche étourdissante !<br />';
								break;
								case 'coup_bouclier' :
									echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> donne un coup de bouclier !<br />';
								break;
								case 'slam' :
									echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> utilises SLAM !<br />';
								break;
								case 'posture_critique' :
								case 'posture_esquive' :
								case 'posture_defense' :
								case 'posture_degat' :
								case 'posture_transperce' :
								case 'posture_paralyse' :
								case 'posture_touche' :
									echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> se met en '.$row['nom'].' !<br />';
								break;
								case 'dissimulation' :
									echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> tente de se dissimuler...';
								break;
								case 'bouclier_protecteur' :
									echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> intensifie sa protection magique grace à son bouclier !<br />';
								break;
								default:
								break;
							}
						}
						elseif($attaque[2] == "p")
							echo '<strong>'.${$mode}->get_nom().'</strong> est paralysé<br />';
						elseif($attaque[2] == "e")
							echo '<strong>'.${$mode}->get_nom().'</strong> est étourdi<br />';
						elseif($attaque[2] == "g")
							echo ${$mode}->get_nom().' est glacé<br />';
						
						if($attaque[3] == "!")
							echo '&nbsp;&nbsp;<span class="coupcritique">COUP CRITIQUE !</span><br />';
						if($attaque[5] == "e") // Si c'est une esquive
							echo "&nbsp;&nbsp;<span class=\"manque\"><strong>".${$mode}->get_nom()."</strong> manque la cible</span><br />";
						elseif($attaque[5] != NULL)
							echo "&nbsp;&nbsp;<span class=\"degat\"><strong>".${$mode}->get_nom()."</strong> inflige ".$attaque[5]." dégâts</span><br />";

				}
				elseif($attaque[1] == "s") // Un sort
				{
					$requete = "SELECT * FROM sort_combat WHERE id = ".$attaque[2];
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					
					if($attaque[5] == "m") // Si c'est une esquive
					{
						echo "&nbsp;&nbsp;<span class=\"manque\"><strong>".${$mode}->get_nom()."</strong> manque la cible avec ".$row['nom']."</span><br />";
					}
					elseif($attaque[5] == "l") // Si c'est un sort raté
					{
						echo '&nbsp;&nbsp;<span class="manque">'.${$mode}->get_nom().' rate le lancement de '.$row['nom'].'</span><br />';
					}
					else
					{
						if($attaque[3] == "!")
							echo '&nbsp;&nbsp;<span class="coupcritique">SORT CRITIQUE !</span><br />';
							
							switch($row['type'])
							{
							case 'degat_feu' :
							case 'degat_nature' :
							case 'degat_mort' :
							case 'degat_froid' :
							case 'degat_vent' :
							case 'degat_terre' :
							case 'lapidation' :
							case 'globe_foudre' :
							case 'putrefaction' :
							case 'brisement_os' :
							case 'sphere_glace' :
								echo '&nbsp;&nbsp;<span class="degat"><strong>'.${$mode}->get_nom().'</strong> inflige <strong>'.$attaque[5].'</strong> dégâts avec '.$row['nom'].'</span><br />';
							break;
							case 'embrasement' :
								echo '&nbsp;&nbsp;<span class="degat"><strong>'.${$mode}->get_nom().'</strong> inflige <strong>'.$attaque[5].'</strong> dégâts avec '.$row['nom'].'</span><br />';
							break;
							case 'sacrifice_morbide' :
								echo '&nbsp;&nbsp;<span class="degat"><strong>'.${$mode}->get_nom().'</strong> se suicide et inflige <strong>'.$attaque[5].'</strong> dégâts avec '.$row['nom'].'</span><br />';
							break;
							case 'pacte_sang' :
								if($attaque[5] > 0)
								{
									echo '&nbsp;&nbsp;<span class="degat"><strong>'.${$mode}->get_nom().'</strong> inflige <strong>'.$attaque[5].'</strong> dégâts avec '.$row['nom'].'</span><br />';
								}
							break;
							case 'drain_vie' :
								$drain = round($attaque[5] * 0.3);
								echo '&nbsp;&nbsp;<span class="degat"><strong>'.${$mode}->get_nom().'</strong> inflige <strong>'.$attaque[5].'</strong> dégâts avec '.$row['nom'].'<br />
								Et gagne <strong>'.$drain.'</strong> hp grâce au drain</span><br />';
							break;
							case 'vortex_vie' :
								$drain = round($attaque[5] * 0.4);
								echo '&nbsp;&nbsp;<span class="degat"><strong>'.${$mode}->get_nom().'</strong> inflige <strong>'.$attaque[5].'</strong> dégâts avec '.$row['nom'].'<br />
								Et gagne <strong>'.$drain.'</strong> hp grâce au drain</span><br />';
							break;
							case 'vortex_mana' :
								$drain = round($attaque[5] * 0.2);
								echo '&nbsp;&nbsp;<span class="degat"><strong>'.${$mode}->get_nom().'</strong> inflige <strong>'.$attaque[5].'</strong> dégâts avec '.$row['nom'].'<br />
								Et gagne <strong>'.$drain.'</strong> RM grâce au drain</span><br />';
                break;
							case 'brulure_mana' :
								$brule_mana = $row['effet'];
								echo '&nbsp;&nbsp;<span class="degat"><strong>'.${$mode}->get_nom().'</strong> retire '.$brule_mana.' réserve de mana et inflige <strong>'.$attaque[5].'</strong> dégâts avec '.$row['nom'].'</span><br />';
							break;
							case 'appel_tenebre' :
								echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> lance le sort '.$row['nom'].'.<br />';
							break;
							case 'appel_foret' :
								echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> lance le sort '.$row['nom'].'.<br />';
							break;
							case 'benediction' :
								echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> se lance le sort '.$row['nom'].'<br />';
							break;
							case 'paralysie' :
								echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> lance le sort '.$row['nom'].'<br />';
							break;
							case 'silence' :
								echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> lance le sort '.$row['nom'].'<br />';
							break;
							case 'lien_sylvestre' :
								echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> lance le sort '.$row['nom'].'<br />';
							break;
							case 'poison' :
								echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> lance le sort '.$row['nom'].'<br />';
							break;
							case 'jet_acide' :
								echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> lance le sort jet d\'acide<br />';
							break;
							case 'recuperation' :
								echo '&nbsp;&nbsp;<strong>'.${$mode}->get_nom().'</strong> se lance le sort '.$row['nom'].'<br />';
							break;
							case 'aura_feu' :
								echo '&nbsp;&nbsp;Une enveloppe de feu entoure <strong>'.${$mode}->get_nom().'</strong> !<br />';
							break;
							case 'aura_glace' :
								echo '&nbsp;&nbsp;Une enveloppe de glace entoure <strong>'.${$mode}->get_nom().'</strong> !<br />';
							break;
							case 'aura_vent' :
								echo '&nbsp;&nbsp;Des tourbillons d\'air entourent <strong>'.${$mode}->get_nom().'</strong> !<br />';
							break;
							case 'aura_pierre' :
								echo '&nbsp;&nbsp;De solides pierres volent autour de <strong>'.${$mode}->get_nom().'</strong> !<br />';
							break;
						}
					}
				}
				elseif($attaque[1] == "a" AND $mode == 'attaquant')
					echo $defenseur->get_nom().' anticipe l\'attaque, et elle échoue !<br />';
				elseif($attaque[1] == "a" AND $mode == 'defenseur')
					echo $attaquant->get_nom().' anticipe l\'attaque, et elle échoue !<br />';
				elseif ($attaque[1] == 'n')
					echo ${$mode}->get_nom().' s\'approche<br />';

				// On gère les effets
				if($mode == 'defenseur')
				{
				preg_match("#&ef([0-9]+)~([0-9]+)#i", $combat[$round]['effects_attaquant'], $effects_a);
				preg_match("#&ef([0-9]+)~([0-9]+)#i", $combat[$round]['effects_defenseur'], $effects_d);
				/*
				$effects_a[1] => 7 // id de l'effet
				$effects_a[2] => 4 // valeur des degats 
				*/
						// Armure d'epine
						if($effects_d[1] == "9")
							if($effects_d[2] > 0) echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' renvoie '.$effects_d[2].' dégâts grâce à l\'Armure en épine</span><br />';
						if($effects_a[1] == "9")
							if($effects_a[2] > 0) echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' renvoie '.$effects_a[2].' dégâts grâce à l\'Armure en épine</span><br />';
						// Rage vampirique
						if($effects_d[1] == "8")
							if($effects_d[2] > 0) echo '&nbsp;&nbsp;<span class="soin">'.$defenseur->get_nom().' gagne '.$effects_d[2].' HP par la rage vampirique</span><br />';
						if($effects_a[1] == "8")
							if($effects_a[2] > 0) echo '&nbsp;&nbsp;<span class="soin">'.$attaquant->get_nom().' gagne '.$effects_a[2].' HP par la rage vampirique</span><br />';
						//Perte de HP par le poison
						if($effects_a[1] == "1")
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$effects_a[2].' HP par le poison</span><br />';
						if($effects_d[1] == "1")
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$effects_d[2].' HP par le poison</span><br />';
						//Perte de HP par hémorragie
						if($effects_a[1] == "2")
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$effects_a[2].' HP par hémorragie</span><br />';
						if($effects_d[1] == "2")
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$effects_d[2].' HP par hémorragie</span><br />';
						//Perte de HP par embrasement
						if($effects_a[1] == "3")
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$effects_a[2].' HP par embrasement</span><br />';
						if($effects_d[1] == "3")
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$effects_d[2].' HP par embrasement</span><br />';
						//Perte de HP par acide
						if($effects_a[1] == "4")
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$effects_a[2].' HP par acide</span><br />';
						if($effects_d[1] == "4")
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$effects_d[2].' HP par acide</span><br />';
						//Perte de HP par lien sylvestre
						if($effects_a[1] == "5")
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$effects_a[2].' HP par le lien sylvestre</span><br />';
						if($effects_d[1] == "5")
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$effects_d[2].' HP par le lien sylvestre</span><br />';
						if($effects_a[1] == "6")
							echo '&nbsp;&nbsp;<span class="soin">'.$attaquant->get_nom().' gagne '.$effects_a[2].' HP par récupération</span><br />';
						if($effects_d[1] == "6")
							echo '&nbsp;&nbsp;<span class="soin">'.$defenseur->get_nom().' gagne '.$effects_d[2].' HP par récupération</span><br />';
						if($effects_d[1] == "7")
							echo '&nbsp;&nbsp;<span class="soin">'.$defenseur->get_nom().' est sous l\'effet de Flêche Débilisante</span><br />';
						if($effects_a[1] == "7")
							echo '&nbsp;&nbsp;<span class="soin">'.$attaquant->get_nom().' est sous l\'effet de Flêche Débilisante</span><br />';
					}
					
				echo '</div>';
				
				if($mode == 'defenseur')
				{
				$round++;
				?>
						</td>
					</tr>
				</table>
				<?php
				}
			}
			
			if($mode == 'attaquant')
			{
			?>
					</td>
				</tr>
			</table>
			<?php
			}
			return true;
		}
		else
			return false;
			
	}
}
?>
