<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: IndexedByDate.php 2067 2010-04-09 09:18:01Z matt $
 * 
 * @category Piwik
 * @package Piwik
 */

/**
 * @package Piwik
 * @subpackage Piwik_Archive
 */
class Piwik_Archive_Array_IndexedByDate extends Piwik_Archive_Array 
{
	/**
	 * Builds an array of Piwik_Archive of a given date range
	 *
	 * @param Piwik_Site $oSite 
	 * @param string $strPeriod eg. 'day' 'week' etc.
	 * @param string $strDate A date range, eg. 'last10', 'previous5' or 'YYYY-MM-DD,YYYY-MM-DD'
	 */
	function __construct(Piwik_Site $oSite, $strPeriod, $strDate)
	{
		$rangePeriod = new Piwik_Period_Range($strPeriod, $strDate, $oSite->getTimezone());
		foreach($rangePeriod->getSubperiods() as $subPeriod)
		{
			$startDate = $subPeriod->getDateStart();
			$archive = Piwik_Archive::build($oSite->getId(), $strPeriod, $startDate );
			$archive->prepareArchive();
			$timestamp = $archive->getTimestampStartDate();
			$this->archives[$timestamp] = $archive;
		}
		ksort( $this->archives );
	}
	
	protected function getIndexName()
	{
		return 'date';
	}
	
	protected function loadMetadata(Piwik_DataTable_Array $table, Piwik_Archive $archive)
	{
		$table->metadata[$archive->getPrettyDate()] = array( 
				'timestamp' => $archive->getTimestampStartDate(),
				'site' => $archive->getSite(),
				'period' => $archive->getPeriod(),
			);
	}
	
	protected function getDataTableLabelValue( $archive )
	{
		return $archive->getPrettyDate();
	}
	
	/**
	 * Given a list of fields defining numeric values, it will return a Piwik_DataTable_Array
	 * which is an array of Piwik_DataTable_Simple, ordered by chronological order
	 *
	 * @param array|string $fields array( fieldName1, fieldName2, ...)  Names of the mysql table fields to load
	 * @return Piwik_DataTable_Array
	 */
	public function getDataTableFromNumeric( $fields )
	{
		$inNames = $this->getSqlStringFieldsArray($fields);
		
		// we select in different shots
		// one per distinct table (case we select last 300 days, maybe we will  select from 10 different tables)
		$queries = array();
		foreach($this->archives as $archive) 
		{
			if(!$archive->isThereSomeVisits)
			{
				continue;
			}
			
			$table = $archive->archiveProcessing->getTableArchiveNumericName();

			// for every query store IDs
			$queries[$table][] = $archive->getIdArchive();
		}
		// we select the requested value
		$db = Zend_Registry::get('db');
		
		// date => array( 'field1' =>X, 'field2'=>Y)
		// date2 => array( 'field1' =>X2, 'field2'=>Y2)		
		
		$arrayValues = array();
		foreach($queries as $table => $aIds)
		{
			$inIds = implode(', ', array_filter($aIds));
			if(empty($inIds))
			{
				// Probable timezone configuration error, i.e., mismatch between PHP and MySQL server.
				continue;
			}

			$sql = "SELECT value, name, date1 as startDate
									FROM $table
									WHERE idarchive IN ( $inIds )
										AND name IN ( $inNames )";
			$values = $db->fetchAll($sql);
			foreach($values as $value)
			{
				$timestamp = Piwik_Date::factory($value['startDate'])->getTimestamp();
				$arrayValues[$timestamp][$value['name']] = (float)$value['value'];
			}			
		}
		
		$contentArray = array();
		// we add empty tables so that every requested date has an entry, even if there is nothing
		// example: <result date="2007-01-01" />
		foreach($this->archives as $timestamp => $archive)
		{
			$strDate = $this->archives[$timestamp]->getPrettyDate();
			$contentArray[$timestamp]['table'] = new Piwik_DataTable_Simple();
			$contentArray[$timestamp]['prettyDate'] = $strDate;
		}

		foreach($arrayValues as $timestamp => $aNameValues)
		{
			$contentArray[$timestamp]['table']->addRowsFromArray($aNameValues);
		}
		ksort( $contentArray );
				
		$tableArray = $this->getNewDataTableArray();
		foreach($contentArray as $timestamp => $aData)
		{
			$tableArray->addTable($aData['table'], $aData['prettyDate']);
			$this->loadMetadata($tableArray, $this->archives[$timestamp]);
		}
		return $tableArray;
	}
}
