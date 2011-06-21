<?php  // -*- php -*-
/**
 * @file titres.class.php
 * Gestion des titres joueurs
 */
 
/**
 * Classe représentant un titre joueur
 */

class titre extends perso
{
	private $id_titre ;
	private $id_perso;
	
	function __construct($id = 0,$id_perso = 0, $id_titre = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id))
		{
			$requeteSQL = $db->query('SELECT valeur FROM options WHERE nom = "titre" AND id_perso = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list( $this->id_titre) = $db->read_row($requeteSQL);
			}
			else
			{
				$this->__construct();
			}
			$this->id_perso = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id_perso = $id['id'];
			$this->id_titre = $id['id_titre'];
		}
		else
		{
			$this->id_perso = $id;
			$this->id_titre = $id_achiev;
		}
	}
   function set_id_titre($id_titre)
   {
	   global $db;
		if( $this->id_titre != 0 )
		{
			$requete = 'UPDATE options SET ';
			$requete .= 'valeur = "'.$id_titre.'"';
			$requete .= ' WHERE nom = "titre" AND id_perso = '.$this->id_perso;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO options (id, id_perso, nom, valeur) VALUES("",';
			$requete .= '"'.$this->id_perso.'", "titre",  "'.$id_titre.'")';
			$db->query($requete);
			$this->titre = $id_titre;		
		}
   }
   
   
   function get_id_titre()
   {
	   global $db;
	   $requete = 'SELECT valeur FROM options WHERE nom = "titre" AND id_perso = "'.$this->id_perso.'"';
	   $req = $db->query($requete);
	   $row = $db->read_row($req);
	   return $row[0];	   
	}
	
	
	function get_titre($id_titre)
	{
		global $db;
	   $requete = 'SELECT titre FROM achievement_type WHERE id = "'.$id_titre.'"';
	   $req = $db->query($requete);
	   $row = $db->read_row($req);
	   $explode = explode('-', $row[0]);
	   return $explode;
	}
	
	function get_titre_perso($bonus)
	{			
		$titre_perso[] ='';
		$titre = $this->get_titre($this->get_id_titre());
		if ($titre != NULL & $titre[0] == 'ap')
		{
			if ($titre[2] == 1 )
			{
				$titre_perso[1] = 'dit ';
				if (array_key_exists(12, $bonus))
				{
					$requete = 'SELECT * FROM bonus_perso WHERE id_bonus = "12" AND id_perso = "'.$this->get_id().'"';
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					if ($row['valeur'] == 2)
					{
						$titre_perso[1] .= ' la ';
					}
					else
					{
						$titre_perso[1] .= ' le ';
					}				
				}
				else
				{
					$titre_perso[1] .= ' le ';
				}
				$titre_perso[1] .= $titre[1];
				return $titre_perso;
			}
			else
			{
				$titre_perso[1] = "dit l'".$titre[1];
				return $titre_perso;
			}
		}
		elseif ($titre != NULL & $titre[0] == 'av')
		{
			if ($titre[2] == 1 )
			{
				if (array_key_exists(12, $bonus) )
				{
					$requete = 'SELECT * FROM bonus_perso WHERE id_bonus = "12" AND id_perso = "'.$this->get_id().'"';
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					if ($row['valeur'] == 2)
					{
						$titre_perso[0] = 'La ';
					}
					else
					{
						$titre_perso[0] = 'Le ';
					}				
				}
				else
				{
					$titre_perso[0] .= 'Le ';
				}
				$titre_perso[0] .= $titre[1];
			}
			else
			{
				$titre_perso[0] = "L'".$titre[1];
			}
			return $titre_perso;
		}
	}
}
