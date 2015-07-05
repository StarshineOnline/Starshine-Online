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

	function prend_objet($id_objet, &$perso)
	{
		global $db, $G_erreur, $G_place_inventaire;
		$trouver = false;
		$stack = false;
		$obj = objet_invent::factory($id_objet);
		$objet_d = decompose_objet($id_objet);
		if( $obj->get_stack() > 1 )
		{
			$deb = 'o'.$obj->get_id();
			while($i < count($this->slot_liste) && !$trouver)
			{
				$decomp = explode('x', $this->slot_liste[$i]);
				if( $decomp[0] == $deb )
				{
					$obj2 = objet_invent::factory($this->slot_liste[$i]);
					$nbr = $obj2->get_nombre() + $obj->get_nombre();
					if( $nbr < $obj->get_stack() )
					{
						$trouver = true;
						$stack = true;
						break;
					}
				}
				$i++;
			}
		}
		if(!$trouve)
		{
			$encombrement = $perso->get_encombrement() + $obj->get_encombrement();
			if( $encombrement < $perso->get_max_encombrement() )
			{
				$trouver = true;
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
				$this->slot_liste[] = $obj->get_texte();
				$perso->set_encombrement( $encombrement );
			}
			else
			{
			  // ...à une pile d'objet identiques
				$obj2->set_nombre( $obj2->get_nombre() + $obj->get_nombre() );
				$obj2->recompose_texte();
				$this->slot_liste[$i] = $obj2->get_texte();
			}
			return true;
		}
	}
}

?>