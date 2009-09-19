<?php
if (file_exists('../root.php'))
  include_once('../root.php');

class inventaire
{
	public $cape;
	public $main;
	public $main_droite;
	public $main_gauche;
	public $torse;
	public $tete;
	public $ceinture;
	public $jambe;
	public $chaussure;
	public $liste;
	public $slot_liste;
	function __construct($inventaire, $inventaire_slot)
	{
		if(!is_array($inventaire)) $this->liste = unserialize($inventaire);
		else $this->liste = $inventaire;
		if(!is_array($inventaire_slot)) $this->slot_liste = unserialize($inventaire_slot);
		else $this->slot_liste = $inventaire_slot;
	}

	function prend_objet($id_objet)
	{
		global $db, $G_erreur, $G_place_inventaire;
		$trouver = false;
		$stack = false;
		$objet_d = decompose_objet($id_objet);
		// Maximum d'empilement possible
		if($objet_d['categorie'] != 'o')
		{
		  // Ne peut pas être empilé
			$row['stack'] = 0;
		}
		else
		{
		  // Récupération de la description de l'objet
			$id_reel_objet = $objet_d['id_objet'];
			//Recherche de l'objet
			$requete = "SELECT * FROM objet WHERE id = ".$id_reel_objet;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
		}
		//Recherche si le joueur n'a pas des objets de ce type dans son inventaire
		$i = 0;
		while(($i < $G_place_inventaire) AND !$trouver)
		{
			$objet_i = decompose_objet($this->slot_liste[$i]);
			//echo '$objet_i[\'sans_stack\'] => '.$objet_i['sans_stack'].'  / $objet_d[\'sans_stack\'] => '.$objet_d['sans_stack'].' / intval($objet_i[\'stack\']) => '.intval($objet_i['stack']).' / $row[\'stack\'] => '.$row['stack'];
			// Comparaison de la description ('sans_stack') et du nombre d'objet empilé par rapport au maximum
			if($objet_i['sans_stack'] == $objet_d['sans_stack'] AND $row['stack'] > 1 AND intval($objet_i['stack']) < $row['stack'])
			{
				$trouver = true;
				$stack = true;
			}
			else $i++;
		}
		if(!$trouver)
		{
			//Recherche un emplacement libre
			$i = 0;
			while(($i < $G_place_inventaire) AND !$trouver)
			{
				if($this->slot_liste[$i] === 0 OR $this->slot_liste[$i] == '')
				{
					$trouver = true;
				}
				else $i++;
			}
		}
		if(!$trouver)
		{
		  //Inventaire plein
			$G_erreur = 'Vous n\'avez plus de place dans votre inventaire<br />';
			return false;
		}
		else
		{
			// Ajout de l'objet...
			if(!$stack)
			{
				// ...dans un emplacement vide
				$this->slot_liste[$i] = $id_objet;
			}
			else
			{
			  // ...à une pile d'objet identiques
				$stacks = $objet_i['stack'] + 1;
				if($stacks == 1) $stacks = 2;
				$this->slot_liste[$i] = $objet_i['sans_stack'].'x'.$stacks;
			}
			return true;
		}
	}
}

?>