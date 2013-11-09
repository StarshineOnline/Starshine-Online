<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class bataille_royaume
{
	public $id_royaume;

	function __construct($id_royaume = 0)
	{
		$this->id_royaume = $id_royaume;
	}

	function get_batailles($etat = 'all')
	{
		global $db;
		$this->batailles = array();

		if($etat == 'all') $where = 1;
		else $where = 'etat = '.$etat;
		$requete = "SELECT id, id_royaume, x, y, nom, description, etat, debut, fin FROM bataille WHERE id_royaume = ".$this->id_royaume." AND ".$where." ORDER BY etat ASC, fin ASC";
		$req = $db->query($requete);

		while($row = $db->read_assoc($req))
		{
			$this->batailles[] = new bataille($row);
		}
		return $this->batailles;
	}

	function get_all_repere_type()
	{
		global $db;
		$reperes_type = array();
		$i = 0;

		$requete = "SELECT id, nom, description, ajout_groupe, image FROM bataille_repere_type ORDER BY ajout_groupe DESC";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$reperes_type[$i] = new bataille_repere_type($row);
			$i++;
		}
		return $reperes_type;
	}
}
?>