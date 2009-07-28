<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class coffre extends terrain_construction
{
	function get_coffre_inventaire()
	{
		global $db;
		$this->coffre_inventaire = array();
		$requete = "SELECT id, id_coffre, objet, nombre FROM terrain_coffre WHERE id_coffre = ".$this->id;
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$this->coffre_inventaire[] = new terrain_coffre($row);
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
		if($objet['sans_stack'] != '')
		{
			$stack = false;
			if($objet['categorie'] == 'o')
			{
				$recherche = $this->recherche_objet($objet['id']);
				$requete = "SELECT stack FROM objet WHERE id = ".$objet['id_objet'];
				$req = $db->query($requete);
				$row = $db->read_row($req);
				$stack = intval($row[0]);
			}
			if(!$recherche OR !$stack OR $recherche->nombre >= $stack)
			{
				//Ajout de l'item
				$terrain_coffre = new terrain_coffre();
				$terrain_coffre->id_coffre = $this->id;
				$terrain_coffre->objet = $objet['sans_stack'];
				$terrain_coffre->nombre = 1;
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
}
?>