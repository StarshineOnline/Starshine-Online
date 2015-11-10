<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class coffre extends terrain_construction
{
	protected $encombrement;
	
	function get_encombrement()
	{
		return $this->encombrement;
	}
	
	function get_coffre_inventaire()
	{
		global $db;
		$this->coffre_inventaire = array();
		$requete = "SELECT id, id_coffre, objet, nombre FROM terrain_coffre WHERE id_coffre = ".$this->id;
		$req = $db->query($requete);
		$this->encombrement = 0;
		while($row = $db->read_assoc($req))
		{
			$tc = new terrain_coffre($row);
			$this->coffre_inventaire[] = $tc;
			$this->encombrement += $tc->encombrement;
		}
		return $this->coffre_inventaire;
	}

	function recherche_objet($id_objet)
	{
		global $db;
		$requete = "SELECT id, id_coffre, objet, nombre FROM terrain_coffre WHERE id_coffre = ".$this->id." AND objet = '".$id_objet."' ORDER BY nombre ASC";
		$req = $db->query($requete);
		if($db->num_rows > 0)
		{
			$row = $db->read_assoc($req);
			return new terrain_coffre($row['id'], $row['id_coffre'], $row['objet'], $row['nombre']);
		}
		else return false;
	}
	
	function depose_objet($objet)
	{
		global $db;
		$stack = false;
		$recherche = $objet->get_stack() > 1 ? $this->recherche_objet($objet->get_texte_id()) : null;
		if(!$recherche || $recherche->nombre >= $objet->get_stack())
		{
			//Ajout de l'item
			$terrain_coffre = new terrain_coffre();
			$terrain_coffre->id_coffre = $this->id;
			$terrain_coffre->objet = $objet->get_texte_id();
			$terrain_coffre->nombre = 1;
			$terrain_coffre->encombrement = $objet->get_encombrement();
			$terrain_coffre->sauver();
		}
		else
		{
			//Sinon on met à jour
			$recherche->nombre++;
			$recherche->sauver();
		}
	}
}
?>