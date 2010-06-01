<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: TablePartitioning.php 1613 2009-11-27 20:57:13Z vipsoft $
 * 
 * @category Piwik
 * @package Piwik
 */

/**
 * 
 * NB: When a new table is partitionned using this class, we have to update the method
 *     Piwik::getTablesInstalled() to add the new table to the list of tablename_* to fetch
 * 
 * @package Piwik
 * @subpackage Piwik_TablePartitioning
 */
abstract class Piwik_TablePartitioning
{
	protected $tableName = null;
	protected $generatedTableName = null;
	protected $timestamp = null;
	protected $idSite = null;
	
	static public $tablesAlreadyInstalled = null;
	
	public function __construct( $tableName )
	{
		$this->tableName = $tableName;
	}
	
	abstract protected function generateTableName() ;
	
	public function setTimestamp( $timestamp )
	{
		$this->timestamp = $timestamp;
		$this->generatedTableName = null;
		$this->getTableName();
	}
	
	public function setIdSite($idSite)
	{
		$this->idSite = $idSite;
	}
		
	public function getTableName()
	{
		// table name already processed
		if(!is_null($this->generatedTableName))
		{
			return $this->generatedTableName;
		}
		
		if(is_null($this->timestamp))
		{
			throw new Exception("You have to specify a timestamp for a Table Partitioning by date.");
		}
		
		// generate table name
		$this->generatedTableName = $this->generateTableName();
		 
		// we make sure the table already exists
		$this->checkTableExists();
	}
	
	protected function checkTableExists()
	{
		if(is_null(self::$tablesAlreadyInstalled))
		{
			self::$tablesAlreadyInstalled = Piwik::getTablesInstalled($forceReload = false, $this->idSite);
		}
		
		if(!in_array($this->generatedTableName, self::$tablesAlreadyInstalled))
		{
			$db = Zend_Registry::get('db');
			$sql = Piwik::getTableCreateSql($this->tableName);
			
			$config = Zend_Registry::get('config');
			$prefixTables = $config->database->tables_prefix;
			$sql = str_replace( $prefixTables . $this->tableName, $this->generatedTableName, $sql);
			$sql = "/* SHARDING_ID_SITE = ".$this->idSite." */ ".$sql;
			$db->query( $sql );
			
			self::$tablesAlreadyInstalled[] = $this->generatedTableName;
		}
	}
	
	public function __toString()
	{
		return $this->getTableName();
	}
}

/**
 * 
 * @package Piwik
 * @subpackage Piwik_TablePartitioning
 */
class Piwik_TablePartitioning_Monthly extends Piwik_TablePartitioning
{
	public function __construct( $tableName )
	{
		parent::__construct($tableName);
	}
	protected function generateTableName()
	{
		$config = Zend_Registry::get('config');
		return $config->database->tables_prefix . $this->tableName . "_" . date("Y_m", $this->timestamp);
	}
		
}

/**
 * 
 * @package Piwik
 * @subpackage Piwik_TablePartitioning
 */
class Piwik_TablePartitioning_Daily extends Piwik_TablePartitioning
{
	public function __construct( $tableName )
	{
		parent::__construct($tableName);
	}
	protected function generateTableName()
	{
		$config = Zend_Registry::get('config');
		return $config->database->tables_prefix . $this->tableName . "_" . date("Y_m_d", $this->timestamp);
	}		
}
