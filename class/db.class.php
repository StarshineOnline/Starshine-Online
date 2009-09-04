<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
/**
  Accord de licence et d'utilisation

  L'intégralité de cette class est distribuer sous licence GPL
  Pour savoir ce que vous avez le droit de faire et ne pas faire avec cette class, reportez
  vous à la licence GPL :
    http://www.gnu.org/licenses/gpl.txt

  !!! TOUTE UTILISATION EN DEHORS DES TERMES DE LA LICENCE GPL SERAS PASSIBLES DE POURSUITE JUDICIAIRE !!!
*/

//! Class db
/**
 * Objet db
 *
 * Le but de cette class est de permettre une implémentation facile de la connexion à une base de donnée en PHP
 * Initialement développé pour la base mySQL, cette classe permet au développeur une plus grande souplesse et lisibilité
 * dans son code.
 *
 * Dernier développement effectué sur : PHP 5.0.4
 * Testé sur :
 * - PHP 5.0.x
 *
 * Dernière maj : 17/08/2005
 *
 * @access public
 * @author KisSCoOl <kisscool@kisscool.net>
 * @version 1.3
 * @copyright Copyright 2003-2005, KisSCoOl
 * @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
 */
class db
{
	private $lnk;                	// (ressource)    : lien à la base, retourner par mysql_connect
	private $sql;                 // (ressource)    : résultat d'une requète SQL, retourner par mysql_query
	private $type;                // (string)       : type de la base de donnée
	private $string_type;         // (array string) : type d'objet à traiter en string

	public $num_rows;            	// (int)          : nombre de lignes retourner par l'éxecution d'une requète
	public $rows_affected;       	// (int)          : nombre de lignes affectés par la dernière requète
	public $num_fields;          	// (int)          : nombre de champs de la table de la dernière requète
	public $default_table_type;  	// (array string) : type par défaut des colonnes d'une table
	public $can_be_null;         	// (array bool)   : tableau contenant la nullité du champ
	public $auto_increment;				// (array bool)		: tableau contenant la liste des champs en auto_increment
	public $nb_query;            	// (int)          : nombre de requète executer par l'objet
	public $encoding;							// (string)				: encoding de la connection
  private $lockname;
  private $locked;

	//! Constructeur
	/**
	Le constructeur de la class db créer un lien vers le serveur de base de donnée configurer dans la global $cg["sql"].
	Il initialise les variables internes suivantes : lnk, type, string_type, nb_query.
	La variable $cfg doit avoir été déclarer en global précédemment.
	*/
	function db($cfg)
	{
	  $this->lockname = null;
	  $this->locked = false;
		$this->lnk = @mysql_connect($cfg["sql"]["host"].":".$cfg["sql"]["port"], $cfg["sql"]["user"], $cfg["sql"]["pass"], true) or die("Le serveur de données est en cours de mise à jour ...<br />Merci de revenir dans quelques minutes ...");
		@mysql_select_db($cfg["sql"]["db"], $this->lnk) or die("La base de données est en cours de mise à jour ...<br />Merci de revenir dans quelques minutes ...");

		// initialisation des variables par défaut
		$this->type         = isset($cfg["sql"]["type"])        ? $cfg["sql"]["type"]         :"mysql";
		$this->string_type  = isset($cfg["sql"]["string_type"]) ? $cfg["sql"]["string_type"]  :"";
		$this->nb_query     = 0;

		$this->encoding = isset($cfg["sql"]["encoding"]) ? $cfg["sql"]["encoding"] : 'utf8';
		$curenc = mysql_client_encoding($this->lnk);
		if ($curenc != $this->encoding) {
			if (function_exists('mysql_set_charset')) {
				mysql_set_charset($this->encoding, $this->lnk);
			}
		}
	}

	//! Executer une requète
	/**
	La fonction query execute une requète SQL, en effectue quelques vérifications d'usages.
	Elle met à jour les propriétés suivantes : sql, nb_query, num_rows, num_fields, rows_affected.
	En cas d'erreur, la méthode arrête le script en cours en renvois un message d'erreur, ainsi qu'un mail d'avertissement.
	*/
	function query($query)
	{
		if( !is_resource($this->lnk) )
			$this->db();

		//error_log($query);

		$query_type = strtoupper( substr($query, 0, strpos($query," ")) );

		//echo $query."<br />";

		// On utilise mysql_real_escape_string pour protéger la requète si cette fonction existe
		//if( function_exists("mysql_real_escape_string") )
		//	$this->sql = mysql_query( mysql_real_escape_string($query,$this->lnk), $this->lnk);
		//else
		//{
		$this->protect_query($query);
		$this->sql = mysql_query($query, $this->lnk);
		//}

		/*
		if (strpos($query, 'esquive') && $query_type == 'UPDATE') {
      $this->backtrace($query);
		}
		*/

		// La requète à réussis, mise à jour des propriétés de la classe
		if($this->sql != false)
		{
			$this->nb_query++;

			if( in_array($query_type, array("SELECT","SHOW") ) )
			{
				$this->num_rows = mysql_num_rows($this->sql);
				$this->num_fields = mysql_num_fields($this->sql);
			}
			else $this->rows_affected = mysql_affected_rows($this->lnk);

			/*
			if($query_type == "DELETE")
			{
			  $from = substr($query,strpos($query,"FROM")+5,strpos($query," ",5)-1);
			  mysql_query("OPTIMIZE TABLE `".$from."`");
			}
			*/
		}
		// La requète à échouer, affichage message d'erreur et utilisation errorlib si charger
		else
		{
			echo "Impossible d'executer la requète suivante:<br />".$query."<br />mySQL a répondus : <span style=\"font-style: italic;\">Erreur n°<span style=\"font-weight: 700;\">".mysql_errno()."</span></span> ".mysql_error();

      $this->backtrace();

			if( function_exists("userErrorHandler") )
			{
				set_error_handler("userErrorHandler");
				trigger_error("Erreur 'SQL_QUERY': ".basename($_SERVER["PHP_SELF"])."?".$_SERVER["QUERY_STRING"]."\nQuery: ".$query."\nErreur:".mysql_errno()." (".mysql_error().")",E_USER_ERROR);
			}
			if ($this->locked) $this->unlock();
			exit();
		}
		return $this->sql;
	}

  function lock($name)
  {
    if ($this->locked) return false;
    $this->lockname = $name;
    $cnt = 0;
    while ($this->is_locked()) {
      sleep(1);
      if ($cnt++ == 5)
	return false;
    }
    $res = $this->query("SELECT GET_LOCK('$name', 5)");
    $a = $this->read_array($res);
    if ($a[0] == 1)
      $this->locked = true;
    else
      $this->locked = false;
    return $this->locked;
  }
  
  function unlock()
  {
    if ($this->locked == false) return false;
    $res = $this->query("SELECT RELEASE_LOCK('$this->lockname')");
    $a= $this->read_array($res);
    if($a[0] == 1)
      $this->locked = false;
    else
      $this->locked = true;
    return !$this->locked;
  }

  function is_locked()
  {
    if ($this->lockname == null) return false;
    $res = $this->query("SELECT IS_FREE_LOCK('$this->lockname')");
    $a= $this->read_array($res);
    if ($a[0] == 1) return false;
    else return true;
  }

  function backtrace($query = null) {
			$back = debug_backtrace();
      if ($query != null)
        echo "QUERY: $query<br/>";
			foreach ($back as $f) {
				echo $f["file"].' line '.$f["line"].'<br />';
			}
			echo '<br />';
  }

	//! check_query
	/**
	 * Cette fonction à pour but d'implémenter une vérification syntaxique de la requète.
	 * Son but premier est d'éviter que des requètes volontairement malformés via les champs input d'un formulaire puissent corrompre la base de données.
	 */
	function protect_query(&$query)
	{
		/*
		if( empty($query) )
		{
		  return false;
		}
		
		if( preg_match("/[^'][\w\s*](;|#)[\w\s*][^']/", $query) == 1 )
		{
			return false;
		}
		*/
		
		return true;
	}

	//! add
	/**
	  Cette fonction permet d'ajouter les valeurs des propriétés d'un objet d'une table correctement définis.
	  La fonction reçoit un argument qui est une class complète. Le nom de cette class est le nom de la table de la base de donnée où travailler.
	  Il doit y'avoir au moins autant de propriété dans la class que de champ dans la table.
	
	  \param $obj : une classe dont le nom est celui de la table où effectuer l'ajout. Les propriétés de l'objet doivent porter les mêmes noms (en minusucle) que les champs de la table.
	*/
	function add($obj)
	{
		// Récupération du nom de la class = nom de la table
		$table_name = get_class($obj);
		
		//echo "La table a adder est '".$table_name."'<br />";
		
		// récupération des types defaults de la table
		$this->get_table_property($table_name);
		
				// on récupère la première ligne pour avoir une liste des champs de la table,
				// et tester si la table existe par la même occasion
		$this->query("SELECT * FROM `".$table_name."` LIMIT 1");
		
		if( $this->sql === false )
		{
		  return false;
		}
		
			  // on va construire la requète grâce à une boucle de récupération automatique des variables
			$add = "INSERT INTO `".$table_name."` VALUES(";
		
			  for($k=0; $k < $this->num_fields; $k++)
			{
			    $var_name = strtolower($this->field_name($k));
			    $field_type = $this->field_type($k);
		
			    if( $this->auto_increment[$k] )
			    {
			    	$add.= "'', ";
			    	continue;
			    }
		
		  // Test si valeur = null
			    if( $this->can_be_null[$k] && is_null($obj->$var_name) )
		    $add .= "NULL, ";
		  else if( !$this->can_be_null[$k] && is_null($obj->$var_name) )
		    $add .= "'', ";
		
		  // Test si valeur = time() à convertir en date
		  else if( in_array($field_type, array("date","datetime","time","year") ) )
		  {
		    if( is_null($obj->$var_name) || empty($obj->$var_name) || ($obj->$var_name == 0))
		      $obj->$var_name = time();
		
		    if( !is_numeric($obj->$var_name) )
		      $add .= "'".$obj->$var_name."', ";
		    else if( in_array($field_type, array("date","datetime") ) )
		      $add .= "FROM_UNIXTIME(".$obj->$var_name."), ";
		    else if( in_array($field_type, array("time") ) )
		      $add .= "SEC_TO_TIME(".$obj->$var_name."), ";
		  }
		
		  // Ajout par défaut, avec test si string ou non
		  else
			      $add .= ( in_array($field_type, $this->string_type)?"'".mysql_real_escape_string(trim($obj->$var_name), $this->lnk)."'":trim($obj->$var_name)).", ";
			  } // fin for
		
			  $add = substr($add,0,strlen($add)-2).")";
		
			  if( defined("TEST") && TEST )
			    echo $add.";<br />";
			  else
			    $this->query($add);
		
			  if($this->rows_affected == 1)
			    return true;
			  else
		  return false;
	}
	/* fin function add() */


  /* function update() */
  function update($obj,$lead_id="")
  {
		$table_name = get_class($obj);

		//echo "La table a updater est '".$table_name."'<br />";

		// récupération des types defaults de la table
    $this->get_table_property($table_name);

		// on récupère la première ligne pour avoir une liste des champs de la table
    $this->query("SELECT * FROM `".$table_name."` LIMIT 1");

    if( $this->sql === false )
      return false;

	  // on va construire la requète grâce à une boucle de récupération automatique des variables
  	$update = "UPDATE `".$table_name."` SET ";

    // récupération automatique de l'ID de la table si non spécifié en argument
    // l'ID est considérer comme étant la première colonne
    if( empty($lead_id) )
      $lead_id = strtolower($this->field_name(0));

	  for($k=0; $k < $this->num_fields; $k++)
  	{
	    $var_name = strtolower($this->field_name($k));
	    $field_type = $this->field_type($k);

	    if( $var_name == $lead_id )
	    	continue;

      // Test si valeur = null
      // si la valeur est null, et que le champ peut accepter null, alors on fixe à NULL
      // si la valeur est null, et que le champ ne peut pas accepter null, alors on passe (on ne modifie pas la valeur)

	    if( $this->can_be_null[$k] && is_null($obj->$var_name) )
        $update .= "`".$var_name."`=NULL, ";
      else if( !$this->can_be_null[$k] && is_null($obj->$var_name) )
        continue;

      // Test si valeur = time() à convertir en date
      else if( in_array($field_type,array("date","datetime","time","year") ) )
      {
        if( is_null($obj->$var_name) || empty($obj->$var_name) || ($obj->$var_name == 0))
          $obj->$var_name = time();

        if( !is_numeric($obj->$var_name) )
          $update .= "`".$var_name."`='".$obj->$var_name."', ";
        else if( in_array($field_type, array("date","datetime") ) )
          $update .= "`".$var_name."`=FROM_UNIXTIME(".$obj->$var_name."), ";
        else if( in_array($field_type, array("time") ) )
          $update .= "`".$var_name."`=SEC_TO_TIME(".$obj->$var_name."), ";
      }

      // Ajout par défaut, avec test si string ou non
      else
	      $update .= "`".$var_name."`=".( in_array($field_type,$this->string_type)?"'".mysql_real_escape_string(trim($obj->$var_name),$this->lnk)."'":trim($obj->$var_name)).", ";

	  } // fin for
	  $update = substr($update,0,strlen($update)-2)." WHERE `".$lead_id."`=".$obj->$lead_id;

    if( defined("TEST") && TEST )
	    echo $update.";<br />";
	  else
	    $this->query($update);

	  if($this->rows_affected == 1)
	    return true;
	  else
	    return false;
  }
  /* fin function update() */



  /* function delete() */
  function delete($obj,$lead_id="")
  {
		$table_name = get_class($obj);

		//echo "La table a deleter est '".$table_name."'<br />";

		// on récupère la première ligne pour avoir une liste des champs de la table
    $this->query("SELECT * FROM `".$table_name."` LIMIT 1");

    if( $this->sql === false )
      return false;


    // récupération automatique de l'ID de la table si non spécifié en argument
    // l'ID est considérer comme étant la première colonne
    if( empty($lead_id) )
      $lead_id = strtolower($this->field_name(0));

    $delete = "DELETE FROM `".$table_name."` WHERE `".$lead_id."`=".$obj->$lead_id;

    if( defined("TEST") && TEST )
	    echo $delete.";<br />";
	  else
	    $this->query($delete);

	  if($this->rows_affected == 1)
	    return true;
	  else
	    return false;
  }
  /* fin function delete() */



  // lit en tableau associatif
  function read_array($sql="")
  {
    $sql = empty($sql)?$this->sql:$sql;

    if( is_resource($sql) ) {
      return mysql_fetch_array($sql);
    }

    return false;
  }

  function read_assoc($sql="")
  {
    $sql = empty($sql)?$this->sql:$sql;

    if( is_resource($sql) )
      return mysql_fetch_assoc($sql);

    return false;
  }
 
  function read_field($sql="")
  {
    $sql = empty($sql)?$this->sql:$sql;

    if( is_resource($sql) )
      return mysql_fetch_field($sql);

    return false;
  }

  function read_row($sql="")
  {
    $sql = empty($sql)?$this->sql:$sql;

    if( is_resource($sql) )
      return mysql_fetch_row($sql);

    return false;
  }

  function read_object($sql="")
  {
    $sql = empty($sql)?$this->sql:$sql;

    if( is_resource($sql) )
      return mysql_fetch_object($sql);

    return false;
  }

  function data_seek($row_number=0, $sql="")
  {
  	$sql = empty($sql) ? $this->sql : $sql;

    if( is_resource($sql) )
			return mysql_data_seek($sql,$row_number);

		return false;
  }

	function field_name($k)
  {
	  return mysql_field_name($this->sql,$k);
  }

  function field_type($k)
  {
    return mysql_field_type($this->sql, $k);
  }

  // retourne les types par défaut des champs d'une table
  // ainsi que la nullité possible d'un champ
  function get_table_property($table)
  {
  	$this->default_table_type = array();
  	$this->can_be_null 				= array();
  	$this->auto_increment 		= array();

    $this->query("SHOW COLUMNS FROM `".$table."`");

    while($row = $this->read_array())
    {
    	$this->default_table_type[] = is_null($row["Default"])						? null:$row["Default"];
    	$this->can_be_null[] 				= ($row["Null"] == "YES")							? true:false;
    	$this->auto_increment[]			= ($row["Extra"] == "auto_increment")	? true:false;
    }
  }

  /**
  * Retourne le nom de la clé primaire d'une table
  */
  function get_primary_key($table)
  {
  	$this->query("SHOW INDEX FROM `".$table."`");

  	while($row = $this->read_array() )
  	{
  		if($row["Key_name"] == "PRIMARY")
  		{
  			return $row["Column_name"];
  		}
  	}

  	return null;
  }

  function field_len($k)
  {
    return mysql_field_len($this->sql,$k);
  }

  function last_insert_id()
  {
    return mysql_insert_id($this->lnk);
  }

  // nombre de ligne retourner
  function num_rows($sql)
  {
    $this->num_rows = mysql_num_rows($sql);
    return $this->num_rows;
  }

  //! Changement des valeurs d'un typ ENUM
  /**
   \brief Cette fonction permet de changer les valeurs autorisés dans un type ENUM.

   \param $table        (string) le nom de la table
   \param $col          (string) le nom de la colonne de type ENUM
   \param $new_enum     [optionnel] (array) ou (string), contient la nouvelle définition du type ENUM dans l'ordre requis, ou si string la seule valeur autorisé. Si vide ou non spécifié, aucun changement ne seras effectuer.
   \param $default      [optionnel] (string) la valeur par défaut à utiliser lors d'un ajout. Si vide ou non spécifié, la première option ENUM seras utilisé comme type par défaut
   \param $empty_value  [optionnel] (boolean) autorise ou non les valeurs vides dans l'ENUM. True par défaut

   \return (boolean) true si la mise à jour à réussis, false si elle a échouer ou si rien n'as été modifier.
   */
  function set_enum($table, $col, $new_enum="", $default="", $empty_value=true)
  {
  	// test des variables obligatoires
  	if( empty($new_enum) )
  	  return false;

		// si $new_enum est une chaine de caractère, c'est qu'il n'y a qu'une seule valeur à ajouter l'ENUM. On créer donc un tableau avec ce seul élément.
  	if( is_string($new_enum) && !empty($new_enum) )
  	  $new_enum = array($new_enum);

		// $new_enum doit être un tableau à ce niveau
  	if( !is_array($new_enum) )
  		return false;

  	// récupéres l'enum actuel
  	$old_enum = $this->get_enum($table,$col);

    // si $empty_value=true, on retire les cases vides du tableau des nouvelles options
  	if( !$empty_value && is_array($new_enum) )
  	{
  	  for($k=0; $k < count($new_enum); $k++)
  	  {
  	    if( empty($new_enum[$k]) )
  	      unset($new_enum[$k]);
  	  }

  	  array_reset_keys($new_enum);
  	}

  	// s'il ne reste plus de valeurs à ajouter, on quitte
  	if( count($new_enum) <= 0 )
  		return false;

    // on garde les nouvelles valeurs, puis on rajoute ces nouvelles valeurs à la liste des valeurs actuelles
  	$only_new_enum = array_diff($new_enum, $old_enum);
  	$tmp_enum = array_merge($old_enum, $only_new_enum);

  	// $enum contient la liste de toutes les valeurs, anciennes et nouvelles, pour pouvoir mettre à jour les données, avant de supprimer les anciennes valeurs
  	// change l'enum par default
  	$tmp_default = $tmp_enum[0];

  	if( defined("TEST") && TEST)
  	  echo "ALTER TABLE `".$table."` CHANGE `".$col."` `".$col."` ENUM('".implode("','",$tmp_enum)."') DEFAULT '".$tmp_default."' NOT NULL<br />";
  	else
  	  $this->query("ALTER TABLE `".$table."` CHANGE `".$col."` `".$col."` ENUM('".implode("','",$tmp_enum)."') DEFAULT '".$tmp_default."' NOT NULL");

  	// mise à jour des valeurs
  	for($k=0; $k < count($tmp_enum); $k++)
  	{
  	  if( isset($new_enum[$k]) && ($new_enum[$k] != $tmp_enum[$k]) )
  	  {
  	    if( defined("TEST") && TEST)
  	      echo "UPDATE `".$table."` SET `".$col."`='".$new_enum[$k]."' WHERE `".$col."`='".$tmp_enum[$k]."'<br />";
  	    else
  	      $this->query("UPDATE `".$table."` SET `".$col."`='".$new_enum[$k]."' WHERE `".$col."`='".$tmp_enum[$k]."'");
  	  }
  	}

  	//if( is_array($new_enum) )
    //  $enum = $new_enum;
  	//else if( is_string($new_enum) && !empty($new_enum) )
  	//  $enum[] = $new_enum;

  	// copie pour test
  	$enum = $new_enum;

		if( empty($default) )
		  $default = $enum[0];

		$enum = "'".implode("','",$enum)."'";

  	// change l'enum
  	if( defined("TEST") && TEST)
  	  echo "ALTER TABLE `".$table."` CHANGE `".$col."` `".$col."` ENUM(".$enum.") DEFAULT '".$default."' NOT NULL<br />";
  	else
  	  $this->query("ALTER TABLE `".$table."` CHANGE `".$col."` `".$col."` ENUM(".$enum.") DEFAULT '".$default."' NOT NULL");

  	return true;
  }

  function get_enum($table,$col)
  {
    $this->query("SHOW COLUMNS FROM ".$table." LIKE '".$col."'");

    if( $this->num_rows > 0 )
    {
      $row = $this->read_row();
      return explode("','",preg_replace("/(enum|set)\('(.+?)'\)/","\\2",$row[1]));
    }

    return array();
  }

  //! Vérifie les droits d'un utilisateur à une table/colonne
  function check_right( $user="", $table="", $droits="" )
  {
    global $db;

    if( empty($user) )
      return false;

    $this->query("SHOW GRANTS FOR ".$user);
  }

  	function table_exists($table = "")
	{
		global $db;
		$req = $this->query("SHOW TABLES LIKE '".$table."'");
  		if($this->num_rows > 0) return true;
  		else return false;
	}
	
  function optimize_db($db="")
  {
  	if( empty($db) )
  	  return false;

    $res_table = mysql_list_tables($db, $this->lnk);
    $this->num_rows = mysql_num_rows($res_table);
    $this->num_fields = mysql_num_fields($res_table);

    $table = array();

    for($k=0; $k < $this->num_rows; $k++)
      $table[] = mysql_tablename($res_table,$k);

    $table = implode(", ",$table);
    $this->query("OPTIMIZE TABLE ".$table);

    return true;
  }

  // libère la mémoire utilisé par la requète
  function free()
  {
    return mysql_free_result($this->sql);
  }

  // ferme une connexion à un serveur msSQL
  function close()
  {
    //echo $this->nb_query;

    if( is_resource($this->sql) )
      $this->free();

    @mysql_close($this->lnk) or die("Impossible de fermer la connection au serveur de données.");
  }

	/**
	* Retourne la version courante du serveur mySQL
	*/
  function get_version($delim="-")
  {
    $version = explode($delim, mysql_get_server_info());

    if( is_array($version) )
      return $version[0];

    return $version;
  }

  /**
  * Retourne la valeur de la propriété privée $lnk
  */
  function get_link()
  {
  	return $this->lnk;
  }
}

/*
  Cette fonction vérifie que cette classe est charger et utilisable dans un objet
*/
function check_db_global($obj_name="db")
{
  if( !defined("__OBJ_DB_NAME__") ) define("__OBJ_DB_NAME__",$obj_name);

  if( array_key_exists(__OBJ_DB_NAME__, $GLOBALS) && (get_class($GLOBALS[__OBJ_DB_NAME__]) == "db") )
    return true;
  else
    return false;
}
?>
