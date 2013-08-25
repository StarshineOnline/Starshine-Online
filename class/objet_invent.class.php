<?php
if (file_exists('../root.php'))
  include_once('../root.php');

/**
	Classe abstraite représentant un objet
**/
abstract class objet_invent extends table
{
	protected $nom;
	protected $type;
	protected $prix;
	
	//Constructeur
	function __construct($nom, $type, $prix, $id = -1)
	{
		$this->nom = $nom;
		$this->type = $type;
		$this->prix = $prix;
		$this->id = $id;
	}
	
	function getNom()
	{
		return $this->nom;
	}
	
	function getType()
	{
		return $this->type;
	}
	
	function getPrix()
	{
		return $this->prix;
	}
	
	function setNom($newNom)
	{
		$this->nom = $newNom;
	}
	
	function setType($newType)
	{
		$this->type = $newType;
	}
	
	function setPrix($newPrix)
	{
		$this->prix = $newPrix;
	}
	
	//Fonction permettant d'ajouter un nouvel objet dans la base
	abstract public function sauver();
	abstract public function infobulle();
	
	//Suppression générique
	protected function supprimer($table)
	{
		if( $this->id != -1 )
		{
			$requete = 'DELETE FROM '.$table.' WHERE id = '.$this->id;
			$db->query($requete);
		}
	}
	
	//Retourne une chaine permettant de faciliter l'UPDATE
	protected function modifBase()
	{
		return 'nom = "'.$this->nom.'", type = "'.$this->type.'", prix = "'.$this->prix.'"';
	}
	
	//Retourne une chaine facilittant l'insertion
	protected function insertBase()
	{
		return '"'.$this->nom.'", "'.$this->type.'", "'.$this->prix.'"';
	}
	
	function __toString()
	{
		return $this->nom.', '.$this->type.', '.$this->prix;
	}
	
	//Retourne le début et la fin de la chaine de l'infobulle.
	protected function bulleBase($middle)
	{
		$infobulle = '<strong>'.$this->nom.'</strong><br />';
		$infobulle .= '<table><tr><td>Type:</td><td>'.$this->type.'</td></tr>'.$middle;
		$infobulle = '<tr><td>Prix HT:<br /><span class=\'xsmall\'>(en magasin)</span></td><td>'.$this->prix.'</td></tr></table>';
		return $infobulle;
	}
}
?>
