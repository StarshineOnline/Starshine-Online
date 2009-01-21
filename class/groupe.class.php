<?php
class groupe
{
	public $id;
	public $partage;
	public $prochain_loot;
	public $nom;

	function __construct($id = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT partage, prochain_loot, nom FROM groupe WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->partage, $this->prochain_loot, $this->nom) = $db->read_row($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->partage = $id['partage'];
			$this->prochain_loot = $id['prochain_loot'];
			$this->nom = $id['nom'];
			$this->id = $id['id'];
		}
		else
		{
			$this->id = $id;
			$this->partage = $partage;
			$this->prochain_loot = $prochain_loot;
			$this->nom = $nom;
		}
	}
}
?>