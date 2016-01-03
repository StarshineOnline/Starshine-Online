<?php
class messagerie_lus extends table
{
	protected $id_thread;
	protected $id_debut;
	protected $id_fin;
	protected $id_der_lu;
	protected $id_dest;
	protected $type_dest;
	protected $nbr_msg;
	protected $nbr_non_lu;
	
	
	/// renvoie l'id du thread
	function get_id_thread()
	{
		return $this->id_thread;
	}
	/// Modifie l'id du thread
	function set_id_thread($valeur)
	{
		$this->id_thread = $valeur;
		$this->champs_modif[] = 'id_thread';
	}
	
	/// renvoie l'id du premier message
	function get_id_debut()
	{
		return $this->id_debut;
	}
	/// Modifie l'id du premier message
	function set_id_debut($valeur)
	{
		$this->id_debut = $valeur;
		$this->champs_modif[] = 'id_debut';
	}
	
	/// renvoie l'id du dernier message
	function get_id_fin()
	{
		return $this->id_fin;
	}
	/// Modifie l'id du dernier message
	function set_id_fin($valeur)
	{
		$this->id_fin = $valeur;
		$this->champs_modif[] = 'id_fin';
	}
	
	/// renvoie l'id du dernier message lu
	function get_id_der_lu()
	{
		return $this->id_der_lu;
	}
	/// Modifie l'id du dernier message lu
	function set_id_der_lu($valeur)
	{
		$this->id_der_lu = $valeur;
		$this->champs_modif[] = 'id_der_lu';
	}
	
	/// renvoie l'id du destinataire
	function get_id_dest()
	{
		return $this->id_dest;
	}
	/// Modifie l'id du destinataire
	function set_id_dest($valeur)
	{
		$this->id_dest = $valeur;
		$this->champs_modif[] = 'id_dest';
	}
	
	/// renvoie le type du destinataire
	function get_type_dest()
	{
		return $this->type_dest;
	}
	/// Modifie le type du destinataire
	function set_type_dest($valeur)
	{
		$this->type_dest = $valeur;
		$this->champs_modif[] = 'type_dest';
	}
	
	/// renvoie le nombre de messages
	function get_nbr_msg()
	{
		return $this->nbr_msg;
	}
	/// Modifie le nombre de messages
	function set_nbr_msg($valeur)
	{
		$this->nbr_msg = $valeur;
		$this->champs_modif[] = 'nbr_msg';
	}
	/// Ajoute une valeur au nombre de messages
	function add_nbr_msg($valeur)
	{
		$this->nbr_msg += $valeur;
		$this->champs_modif[] = 'nbr_msg';
	}
	
	/// renvoie le nombre de messages non lus
	function get_nbr_non_lu()
	{
		return $this->nbr_non_lu;
	}
	/// Modifie le nombre de messages non lus
	function set_nbr_non_lu($valeur)
	{
		$this->nbr_non_lu = $valeur;
		$this->champs_modif[] = 'nbr_non_lu';
	}
	/// Ajoute une valeur au nombre de messages non lus
	function add_nbr_non_lu($valeur)
	{
		$this->nbr_non_lu += $valeur;
		$this->champs_modif[] = 'nbr_non_lu';
	}
	
	/**	
	 * Constructeur 
	 */
	function __construct($id = 0, $id_thread = 0, $id_debut = 0, $id_fin = 0, $id_der_lu = 0, $id_dest = 0, $type_dest = 'perso', $nbr_msg = 0, $nbr_non_lu = 0)
	{
		//Verification du nombre et du type d'argument pour construire le message adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			$this->id_thread = $id_thread;
			$this->id_debut = $id_debut;
			$this->id_fin = $id_fin;
			$this->id_der_lu = $id_der_lu;
			$this->id_dest = $id_dest;
			$this->type_dest = $type_dest;
			$this->nbr_msg = $nbr_msg;
			$this->nbr_non_lu = $nbr_non_lu;
		}
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		table::init_tab($vals);
		$this->id_thread = $vals['id_thread'];
		$this->id_debut = $vals['id_debut'];
		$this->id_fin = $vals['id_fin'];
		$this->id_der_lu = $vals['id_der_lu'];
		$this->id_dest = $vals['id_dest'];
		$this->type_dest = $vals['type_dest'];
		$this->nbr_msg = $vals['nbr_msg'];
		$this->nbr_non_lu = $vals['nbr_non_lu'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		/// @todo renvoyer directement le tableau
    $tbl['id_thread']='i';
    $tbl['id_debut']='i';
    $tbl['id_fin']='i';
    $tbl['id_der_lu']='i';
    $tbl['id_dest']='i';
    $tbl['type_dest']='s';
    $tbl['nbr_msg']='i';
    $tbl['nbr_non_lu']='i';
		return $tbl;
	}
}
?>