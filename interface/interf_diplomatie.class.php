<?php
/**
 * @file interf_diplomatie.class.php
 * Affichage de la diplomatie
 */
 
/**
 * classe gérant l'affichage de la diplomatie
 */
class interf_diplomatie extends interf_tableau
{
	function __construct()
	{
		global $db, $Gtrad;
		parent::__construct('diplomatie', 'table table-bordered table-condensed');
		
		/// @todo passer à l'objet
		$requete = "SELECT * FROM diplomatie";
		$req = $db->query($requete);
		
		$fields = array();
		while($row = $db->read_field($req))
		{
			$fields[] = $row->name;
		}
		//my_dump($fields);
		
		//$this->nouv_cell('&nbsp;');
		foreach($fields as $field)
		{
			$this->nouv_cell($Gtrad[$field]);
		}
		$this->nouv_cell('Global');
		
		while($row = $db->read_assoc($req))
		{
			$this->nouv_ligne();
			$total = 0;
			foreach($fields as $field)
			{
				if( !is_numeric($row[$field]) )
				{
					$this->set_entete(true);
					$this->nouv_cell( $row[$field] );
					$this->set_entete(false);
				}
				else if( $row[$field] < 4 )
				{
					$this->nouv_cell($Gtrad['diplo'.$row[$field]], false, 'success');
					$total += $row[$field];
				}
				else if( $row[$field] < 7 )
				{
					$this->nouv_cell($Gtrad['diplo'.$row[$field]], false, 'warning');
					$total += $row[$field];
				}
				else if( $row[$field] < 11)
				{
					$this->nouv_cell($Gtrad['diplo'.$row[$field]], false, 'danger');
					$total += $row[$field];
				}
				else
					$this->nouv_cell('X', false, 'info');
			}
			if($total > 55)
				$this->nouv_cell('Belliqueux', false, 'danger');
			else if($total < 45)
				$this->nouv_cell('Pacifiques', false, 'success');
			else
				$this->nouv_cell('Normal', false, 'warning');
		}
	}
}

?>