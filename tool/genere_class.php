<?php // -*- tab-width:2; mode: php -*-
/**
 * Auteur : Florian Mahieu
 * Version : 1.2
 * Description : permet de générer une class associée a une table MySQL
 */
$root = '../';
include($root.'class/db.class.php');
include($root.'connect.php');

if(array_key_exists('table', $_POST))
{
	$table = $_POST['table'];
	ob_start();

$champs = array();
$requete = "SHOW COLUMNS FROM ".$table;
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$champs[] = $row;
}

$liste_champs = array();
$liste_attributs = array();
$liste_attr_ins_bind = array();
$liste_attr_type_bind = array();

//Définition du champ référence
if(true)
{
	$champ_reference = strtolower($champs[0]['Field']);
}
foreach($champs as $key => $champ)
{
  $liste_attr_ins_bind[] = '?';
	if (stristr($champ['Type'], 'varchar') OR stristr($champ['Type'], 'text'))
	{
		$type = 'string';
    $liste_attr_type_bind[] = 's';
	}
  elseif (stristr($champ['Type'], 'int') OR
          stristr($champ['Type'], 'smallint') OR
          stristr($champ['Type'], 'tinyint'))
    $liste_attr_type_bind[] = 'i';
  elseif (stristr($champ['Type'], 'double') OR
          stristr($champ['Type'], 'float') OR
          stristr($champ['Type'], 'muneric'))
    $liste_attr_type_bind[] = 's';
  else
    $liste_attr_type_bind[] = 'b';
    
	if($champ['Field'] != $champ_reference)
	{
		$liste_champs[] = $champ['Field'];
		$liste_attributs[] = '$this->'.$champ['Field'];
		if($type == 'string')
		{
			$liste_attributs_type[] = '"\'.mysql_escape_string($this->'.$champ['Field'].').\'"';
			$liste_update[] = $champ['Field'].' = ?';
		}
		else
		{
			$liste_attributs_type[] = '\'.$this->'.$champ['Field'].'.\'';
			$liste_update[] = $champ['Field'].' = ?';
		}
		$liste_tostring[] = '\''.$champ['Field'].' = \'.$this->'.$champ['Field'];
		$liste_array[] = '$this->'.$champ['Field'].' = $'.$champ_reference.'[\''.$champ['Field'].'\'];
			';
		 $liste[] = '$this->'.$champ['Field'].' = $'.$champ['Field'].';
			';
	}
	if($type == 'string') {
		$liste_arguments[] = '$'.$champ['Field']." = ''";
		$liste_arguments_names[] = '$'.$champ['Field'];
	}
	else {
		$liste_arguments[] = '$'.$champ['Field'].' = 0';
		$liste_arguments_names[] = '$'.$champ['Field'];
	}
	$champs[$key]['Type_doc'] = $champ['Type'];
	$champs[$key]['Type_doc'] = str_ireplace(' unsigned', '', $champs[$key]['Type_doc']);
}
$liste_arguments = implode(', ', $liste_arguments);
$liste_arguments_names = implode(', ', $liste_arguments_names);
$liste_update = implode(", ", $liste_update);
$liste_tostring = implode('.\', \'.', $liste_tostring);
$liste_array = implode('', $liste_array);
$liste = implode('', $liste);
$liste_champs = implode(', ', $liste_champs);
$liste_attributs_insert = implode(", ", $liste_attributs_type);
$liste_attributs = implode(', ', $liste_attributs);
echo '<?php // -*- tab-width:2; mode: php -*-
';
?>
class <?php echo $table; ?>_db
{
  const types = '<?php echo implode('', $liste_attr_type_bind); ?>';
<?php foreach($champs as $champ): ?>
  /**
    * @access private
    * @var <?php echo $champ['Type_doc']; ?>
    */
	private $<?php echo $champ['Field']; ?>;

	<?php endforeach; ?>

/**
	* @access public
<?php foreach($champs as $champ): ?>
	* @param <?php echo $champ['Type_doc'].' '.$champ['Field'] ?> attribut
<?php endforeach;	?>
	* @return none
	*/
	function __construct(<?php echo $liste_arguments; ?>)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($<?php echo $champ_reference; ?>) )
		{
			$SQLStatement = $db->prepare("SELECT <?php echo $liste_champs; ?> FROM <?php echo $table; ?> WHERE <?php echo $champ_reference; ?> = ".$<?php echo $champ_reference; ?>);
      $SQLStatement->bind_result(<?php echo $liste_attributs; ?>);
			//Si l'entite est dans la base, on la charge sinon on crée une nouvelle.
      if ($SQLStatement->fetch() == false)
			{
        $this->__construct();
			}
			$this-><?php echo $champ_reference; ?> = $<?php echo $champ_reference; ?>;
		}
		elseif( (func_num_args() == 1) && is_array($<?php echo $champ_reference; ?>) )
		{
			$this-><?php echo $champ_reference; ?> = $<?php echo $champ_reference; ?>['<?php echo $champ_reference; ?>'];
			<?php echo $liste_array; ?>}
      else
      {
        <?php echo $liste; ?>
        $this-><?php echo $champ_reference; ?> = $<?php echo $champ_reference; ?>;
      }
	}

/**
	* Sauvegarde automatiquement en base de donnée. Si c'est un nouvel objet, INSERT, sinon UPDATE
	* @access public
	* @param bool $force force la mis à jour de tous les attributs de l'objet si true, sinon uniquement ceux qui ont été modifiés
	* @return none
	*/
	function sauver($force = false, $debug = false)
	{
		global $db;
		if( $this-><?php echo $champ_reference; ?> > 0 )
		{
			if(count($this->champs_modif) > 0)
			{
        $requete = 'UPDATE <?php echo $table; ?> SET ';
				$requete .= '<?php echo $liste_update; ?>';
				$requete .= ' WHERE <?php echo $champ_reference; ?> = '.$this-><?php echo $champ_reference; ?>;
				if($debug) echo $requete.';';
				$stmt = $db->prepare($requete);
        $stmt->bind_param(self::types, <?php echo $liste_attributs; ?>);
        $db->execute($stmt);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO <?php echo $table; ?> (<?php echo $liste_champs; ?>) VALUES(';
			$requete .= '<?php echo implode(', ', $liste_attr_ins_bind); ?>)';
			if($debug) echo $requete.';';
      $stmt = $db->prepare($requete);
      $stmt->bind_param(self::types, <?php echo $liste_attributs; ?>);
      $db->execute($stmt);
			//Récuperation du dernier ID inséré.
			$this-><?php echo $champ_reference; ?> = $db->last_insert_id();
		}
	}

/**
	* Supprime de la base de donnée
	* @access public
	* @param none
	* @return none
	*/
	function supprimer()
	{
		global $db;
		if( $this-><?php echo $champ_reference; ?> > 0 )
		{
			$requete = 'DELETE FROM <?php echo $table; ?> WHERE <?php echo $champ_reference; ?> = ?';
      $stmt = $db->prepare($requete);
      $stmt->bind_param('i', $this-><?php echo $champ_reference; ?>);
      $db->execute($stmt);
			$db->query($requete);
		}
	}

/**
	* Crée un tableau d'objets respectant certains critères
	* @access static
	* @param array|string $champs champs servant a trouver les résultats
	* @param array|string $valeurs valeurs servant a trouver les résultats
	* @param string $ordre ordre de tri
	* @param bool|string $keys Si false, stockage en tableau classique, si string stockage avec sous tableau en fonction du champ $keys
	* @return array $return liste d'objets
	*/
	static function create($champs, $valeurs, $ordre = '<?php echo $champ_reference; ?> ASC', $keys = false, $where = false)
	{
		global $db;
		$return = array();
		if(!$where)
		{
			if(!is_array($champs))
			{
				$array_champs[] = $champs;
				$array_valeurs[] = $valeurs;
			}
			else
			{
				$array_champs = $champs;
				$array_valeurs = $valeurs;
			}
			foreach($array_champs as $key => $champ)
			{
				$where[] = $champ .' = \''.$db->escape($array_valeurs[$key]).'\'';
			}
			$where = implode(' AND ', $where);
			if($champs === 0)
			{
				$where = ' 1 ';
			}
		}

		$requete = "SELECT <?php echo $champ_reference; ?>, <?php echo $liste_champs; ?> FROM <?php echo $table; ?> WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new <?php echo $table; ?>($row);
				else $return[$row[$keys]] = new <?php echo $table; ?>($row);
			}
		}
		else $return = array();
		return $return;
	}

/**
	* Affiche l'objet sous forme de string
	* @access public
	* @param none
	* @return string objet en string
	*/
	function __toString()
	{
		return '<?php echo $champ_reference; ?> = '.$this-><?php echo $champ_reference; ?>.<?php echo $liste_tostring; ?>;
	}
	<?php	foreach($champs as $champ): ?>

/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return <?php echo $champ['Type_doc'] ?> $<?php echo $champ['Field'] ?> valeur de l'attribut <?php echo $champ['Field'] ?>
	*/
	function get_<?php echo $champ['Field'] ?>()
	{
		return $this-><?php echo $champ['Field'] ?>;
	}

  <?php endforeach; ?>

	<?php	foreach($champs as $champ): ?>

/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param <?php echo $champ['Type_doc'] ?> $<?php echo $champ['Field'] ?> valeur de l'attribut
	* @return none
	*/
	function set_<?php echo $champ['Field'] ?>($<?php echo $champ['Field'] ?>)
	{
		$this-><?php echo $champ['Field'] ?> = $<?php echo $champ['Field'] ?>;
		$this->champs_modif[] = '<?php echo $champ['Field'] ?>';
	}

  <?php endforeach; ?>

}

class <?php echo $table ?> extends <?php echo $table ?>_db {
  function __construct(<?php echo $liste_arguments ?>) {
    if( (func_num_args() == 1) && (
         is_numeric($<?php echo $champ_reference ?>) || is_array($<?php echo $champ_reference ?>)))
      parent::__construct($<?php echo $champ_reference ?>);
    else
      parent::__construct(<?php echo $liste_arguments_names ?>);
  }

<?php

	//Inclusion des fonctions spécifiques
	$filename= $root.'class/'.$table.'.class.php';
	$new_file = ob_get_contents();
	if(file_exists($filename))
	{
		$file = fopen($filename, "r+");
		$contents = fread($file, filesize($filename));
    $noautooverride = mb_strrchr($contents, '// @DONTAUTOOVERRIDE');
    ob_end_clean();    
    echo 'here';
    if ($noautooverride != '') {
      echo '<h1>file is marked no auto override</h1>Autogen: <pre>'.
        $new_file.'</pre>';
      die ();
    }
		$string = mb_strrchr($contents, '//fonction');
		fclose($file);
    if (empty($string)) {
      $string = '
  //fonction

}
';
    }
	}
	else
	{
		$string = '
  //fonction

}
';
	}
	$new_file .= $string;
	ob_end_clean();
	$file_save = fopen($filename, "w+");
	fwrite($file_save, $new_file);
	fclose($file_save);
	//echo $new_file;
}
//Formulaire
else
{
	?>
	<form method="post" action="">
		<select name="table">
		<?php
		$requete = "SHOW TABLES";
		$req = $db->query($requete);
		while($row = $db->read_array($req))
		{
			echo '<option value="'.$row[0].'">'.$row[0].'</option>';
		}
		?>
		</select>
		<input type="submit" value="Valider" />
	</form>
	<?php
}
?>