<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'class/db.class.php');

$table = $_GET['table'];

$champs = array();
$requete = "SHOW COLUMNS FROM ".$table;
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$champs[] = $row;
}

$liste_champs = array();
$liste_attributs = array();
foreach($champs as $champ)
{
	if($champ['Field'] != 'id' AND $champ['Field'] != 'ID')
	{
		$liste_champs[] = $champ['Field'];
		$liste_attributs[] = '$this->'.$champ['Field'];
		$liste_update[] = $champ['Field'].' = \'.$this->'.$champ['Field'];
		$liste_array[] = '$this->'.$champ['Field'].' = $id[\''.$champ['Field'].'\'];
			';
		 $liste[] = '$this->'.$champ['Field'].' = $'.$champ['Field'].';
			';
	}
	$liste_arguments[] = '$'.$champ['Field'].' = 0';
}
$liste_arguments = implode(', ', $liste_arguments);
$liste_update = implode(".', ", $liste_update);
$liste_array = implode('', $liste_array);
$liste = implode('', $liste);
$liste_champs = implode(', ', $liste_champs);
$liste_attributs_insert = implode(".', '.", $liste_attributs);
$liste_attributs = implode(', ', $liste_attributs);
?>
class <?php echo $table; ?>
{
	<?php
	foreach($champs as $champ)
	{
		echo 'public $'.$champ['Field'].';
	';
	}
	?>

	/**	
		*	Constructeur permettant la création d'un terrain_batiment.
		*	Les valeurs nombrer défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-<?php echo $table; ?>() qui construit un etat "vide".
		*		-<?php echo $table; ?>($id) qui va chercher l'etat dont l'id est $id
		*		-<?php echo $table; ?>($array) qui associe les champs de $array à l'objet.
	**/
	function __construct(<?php echo $liste_arguments; ?>)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT <?php echo $liste_champs; ?> FROM <?php echo $table; ?> WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list(<?php echo $liste_attributs; ?>) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			<?php echo $liste_array; ?>
		}
		else
		{
			<?php echo $liste; ?>
			$this->id = $id;
		}		
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE <?php echo $table; ?> SET ';
			$requete .= '<?php echo $liste_update; ?>;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO <?php echo $table; ?> (<?php echo $liste_champs; ?>) VALUES(';
			$requete .= <?php echo $liste_attributs_insert; ?>.')';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id) = $db->last_insert_id();
		}
	}

	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM <?php echo $table; ?> WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return 'id = '.$this->id.', <?php echo $liste_update; ?>;
	}
}
