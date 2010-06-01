<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: Period.php 2078 2010-04-12 06:05:54Z matt $
 * 
 * @category Piwik
 * @package Piwik
 */

/**
 * Handles the archiving process for a period
 * 
 * This class provides generic methods to archive data for a period (week / month / year).
 * 
 * These methods are called by the plugins that do the logic of archiving their own data. \
 * They hook on the event 'ArchiveProcessing_Period.compute'
 * 
 * @package Piwik
 * @subpackage Piwik_ArchiveProcessing
 */
class Piwik_ArchiveProcessing_Period extends Piwik_ArchiveProcessing
{
	/*
	 * Array of (column name before => column name renamed) of the columns for which sum operation is invalid. 
	 * The summed value is not accurate and these columns will be renamed accordingly.
	 */
	static public $invalidSummedColumnNameToRenamedName = array(
		Piwik_Archive::INDEX_NB_UNIQ_VISITORS => Piwik_Archive::INDEX_SUM_DAILY_NB_UNIQ_VISITORS 
	);
	
	public function __construct()
	{
		parent::__construct();
		$this->debugAlwaysArchive = Zend_Registry::get('config')->Debug->always_archive_data_period;
	}
	
	/**
	 * Sums all values for the given field names $aNames over the period
	 * See @archiveNumericValuesGeneral for more information
	 * 
	 * @param string|array 
	 * @return Piwik_ArchiveProcessing_Record_Numeric
	 * 
	 */
	public function archiveNumericValuesSum( $aNames )
	{
		return $this->archiveNumericValuesGeneral($aNames, 'sum');
	}
	
	/**
	 * Get the maximum value for all values for the given field names $aNames over the period
	 * See @archiveNumericValuesGeneral for more information
	 * 
	 * @param string|array 
	 * @return Piwik_ArchiveProcessing_Record_Numeric
	 * 
	 */
	public function archiveNumericValuesMax( $aNames )
	{
		return $this->archiveNumericValuesGeneral($aNames, 'max');
	}
	
	/**
	 * Given a list of fields names, the method will fetch all their values over the period, and archive them using the given operation.
	 * 
	 * For example if $operationToApply = 'sum' and $aNames = array('nb_visits', 'sum_time_visit')
	 *  it will sum all values of nb_visits for the period (for example give the number of visits for the month by summing the visits of every day)
	 * 
	 * @param array|string $aNames Array of strings or string containg the field names to select
	 * @param string $operationToApply Available operations = sum, max, min 
	 * @return Piwik_ArchiveProcessing_Record_Numeric Returns the record if $aNames is a string, 
	 *  an array of Piwik_ArchiveProcessing_Record_Numeric indexed by their field names if aNames is an array of strings
	 */
	private function archiveNumericValuesGeneral($aNames, $operationToApply)
	{
		if(!is_array($aNames))
		{
			$aNames = array($aNames);
		}
		
		// fetch the numeric values and apply the operation on them
		$results = array();
		foreach($this->archives as $archive)
		{
			foreach($aNames as $name)
			{
				if(!isset($results[$name]))
				{
					$results[$name] = 0;
				}
				$valueToSum = $archive->getNumeric($name);
				
				if($valueToSum !== false)
				{
					switch ($operationToApply) {
						case 'sum':
							$results[$name] += $valueToSum;	
							break;
						case 'max':
							$results[$name] = max($results[$name], $valueToSum);		
							break;
						case 'min':
							$results[$name] = min($results[$name], $valueToSum);		
							break;
						default:
							throw new Exception("Operation not applicable.");
							break;
					}								
				}
			}
		}
		
		// build the Record Numeric objects
		$records = array();
		foreach($results as $name => $value)
		{
			if($name == 'nb_uniq_visitors' && ($this->periodId == Piwik::$idPeriods['week'] || $this->periodId == Piwik::$idPeriods['month']))
			{
			    $value = (float) $this->computeNbUniqVisitors();
			}
			$records[$name] = new Piwik_ArchiveProcessing_Record_Numeric(
													$name, 
													$value
												);
			$this->insertRecord($records[$name]);
		}
		
		// if asked for only one field to sum
		if(count($records) == 1)
		{
			return $records[$name];
		}
		
		// returns the array of records once summed
		return $records;
	}
	
	
	/**
	 * This method will compute the sum of DataTables over the period for the given fields $aRecordName.
	 * The resulting DataTable will be then added to queue of data to be recorded in the database.
	 * It will usually be called in a plugin that listens to the hook 'ArchiveProcessing_Period.compute'
	 * 
	 * For example if $aRecordName = 'UserCountry_country' the method will select all UserCountry_country DataTable for the period
	 * (eg. the 31 dataTable of the last month), sum them, and create the Piwik_ArchiveProcessing_RecordArray so that
	 * the resulting dataTable is AUTOMATICALLY recorded in the database.
	 * 
	 * 
	 * This method works on recursive dataTable. For example for the 'Actions' it will select all subtables of all dataTable of all the sub periods
	 *  and get the sum.
	 * 
	 * It returns an array that gives information about the "final" DataTable. The array gives for every field name, the number of rows in the 
	 *  final DataTable (ie. the number of distinct LABEL over the period) (eg. the number of distinct keywords over the last month)
	 * 
	 * @param string|array Field name(s) of DataTable to select so we can get the sum 
	 * @param array (current_column_name => new_column_name) for columns that must change names when summed (eg. unique visitors go from nb_uniq_visitors to sum_daily_nb_uniq_visitors)
	 * @param int Max row count of parent datatable to archive  
	 * @param int Max row count of children datatable(s) to archive
	 * @param string Column name to sort by, before truncating rows (ie. if there are more rows than the specified max row count) 
	 * 
	 * @return array  array (
	 * 					nameTable1 => number of rows, 
	 *  				nameTable2 => number of rows,
	 * 				)
	 */
	public function archiveDataTable(	$aRecordName, 
										$invalidSummedColumnNameToRenamedName = null,
										$maximumRowsInDataTableLevelZero = null, 
										$maximumRowsInSubDataTable = null,
										$columnToSortByBeforeTruncation = null )
	{
		if(!is_array($aRecordName))
		{
			$aRecordName = array($aRecordName);
		}
		
		$nameToCount = array();
		foreach($aRecordName as $recordName)
		{
			$table = $this->getRecordDataTableSum($recordName, $invalidSummedColumnNameToRenamedName);
			
			$nameToCount[$recordName]['level0'] =  $table->getRowsCount();
			$nameToCount[$recordName]['recursive'] =  $table->getRowsCountRecursive();
			
			$blob = $table->getSerialized( $maximumRowsInDataTableLevelZero, $maximumRowsInSubDataTable, $columnToSortByBeforeTruncation );
			destroy($table);
			$this->insertBlobRecord($recordName, $blob);
		}
		Piwik_DataTable_Manager::getInstance()->deleteAll();
		
		return $nameToCount;
	}

	/**
	 * This method selects all DataTables that have the name $name over the period.
	 * It calls the appropriate methods that sum all these tables together.
	 * The resulting DataTable is returned.
	 *
	 * @param string $name
	 * @param array columns in the array (old name, new name) to be renamed as the sum operation is not valid on them (eg. nb_uniq_visitors->sum_daily_nb_uniq_visitors)
	 * @return Piwik_DataTable
	 */
	protected function getRecordDataTableSum( $name, $invalidSummedColumnNameToRenamedName )
	{
		$table = new Piwik_DataTable();
		foreach($this->archives as $archive)
		{
			$archive->preFetchBlob($name);
			$datatableToSum = $archive->getDataTable($name);
			$archive->loadSubDataTables($name, $datatableToSum);
			$table->addDataTable($datatableToSum);
			$archive->freeBlob($name);
		}
		
		if(is_null($invalidSummedColumnNameToRenamedName))
		{
			$invalidSummedColumnNameToRenamedName = self::$invalidSummedColumnNameToRenamedName;
		}
		foreach($invalidSummedColumnNameToRenamedName as $oldName => $newName)
		{
			$table->renameColumn($oldName, $newName);
		}
		return $table;
	}
	
	protected function initCompute()
	{
		parent::initCompute();
		$this->archives = $this->loadSubperiodsArchive();
	}

	/**
	 * Returns the ID of the archived subperiods.
	 * 
	 * @return array Array of the idArchive of the subperiods
	 */
	protected function loadSubperiodsArchive()
	{
		$periods = array();
		
		// we first compute every subperiod of the archive
		foreach($this->period->getSubperiods() as $period)
		{
			$archivePeriod = new Piwik_Archive_Single();
			$archivePeriod->setSite( $this->site );
			$archivePeriod->setPeriod( $period );
			$archivePeriod->prepareArchive();
			
			$periods[] = $archivePeriod;
		}
		return $periods;
	}
	
	/**
	 * Main method to process logs for a period. 
	 * The only logic done here is computing the number of visits, actions, etc.
	 * 
	 * All the other reports are computed inside plugins listening to the event 'ArchiveProcessing_Period.compute'.
	 * See some of the plugins for an example.
	 */
	protected function compute()
	{		
		$this->archiveNumericValuesMax( 'max_actions' ); 
		$toSum = array(
			'nb_uniq_visitors', 
			'nb_visits',
			'nb_actions', 
			'sum_visit_length',
			'bounce_count',
			'nb_visits_converted',
		);
		$record = $this->archiveNumericValuesSum($toSum);
		
		$nbVisits = $record['nb_visits']->value;
		$nbVisitsConverted = $record['nb_visits_converted']->value;
		$this->isThereSomeVisits = ( $nbVisits > 0);
		if($this->isThereSomeVisits === false)
		{
			return;
		}
		$this->setNumberOfVisits($nbVisits);
		$this->setNumberOfVisitsConverted($nbVisitsConverted);
		Piwik_PostEvent('ArchiveProcessing_Period.compute', $this);		
	}

	protected function computeNbUniqVisitors()
	{
		$query = "
			SELECT count(distinct visitor_idcookie) as nb_uniq_visitors 
			FROM ".$this->logTable."
			WHERE visit_last_action_time >= ?
    				AND visit_last_action_time <= ? 
    				AND idsite = ?";

		return Zend_Registry::get('db')->fetchOne($query, array( $this->getStartDatetimeUTC(), $this->getEndDatetimeUTC(), $this->idsite ));
	}
	
	/**
	 * Called at the end of the archiving process.
	 * Does some cleaning job in the database.
	 */
	protected function postCompute()
	{
		parent::postCompute();
		
		foreach($this->archives as $archive)
		{
			destroy($archive);
		}
		$this->archives = array();
		
		$blobTable = $this->tableArchiveBlob->getTableName();
		$numericTable = $this->tableArchiveNumeric->getTableName();
		
		$key = 'lastPurge_' . $blobTable;
		$timestamp = Piwik_GetOption($key); 
		if(!$timestamp 
			|| $timestamp < time() - 86400)
		{
			Piwik_SetOption($key, time());
			
			// we delete out of date daily archives from table, maximum once per day
			// we only delete archives processed that are older than 1 day, to not delete archives we just processed
			$yesterday = Piwik_Date::factory('yesterday')->getDateTime();
			$result = Piwik_FetchAll("
							SELECT idarchive
							FROM $numericTable
							WHERE name='done'
								AND value = ". Piwik_ArchiveProcessing::DONE_OK_TEMPORARY ."
								AND ts_archived < ?", array($yesterday));
			
			$idArchivesToDelete = array();
			if(!empty($result))
			{
    			foreach($result as $row) {
    				$idArchivesToDelete[] = $row['idarchive'];
    			}
    			$query = "/* SHARDING_ID_SITE = ".$this->idsite." */ 	
    						DELETE 
    						FROM %s
    						WHERE idarchive IN (".implode(',',$idArchivesToDelete).")
    						";
    			
    			Piwik_Query(sprintf($query, $blobTable));
    			Piwik_Query(sprintf($query, $numericTable));
			}
			Piwik::log("Purging temporary archives: done [ purged archives older than $yesterday from $blobTable and $numericTable ] [Deleted IDs: ". implode(',',$idArchivesToDelete)."]");
		}
		else
		{
			Piwik::log("Purging temporary archives: skipped.");
		}
		
	}	
}
