<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: RecordArray.php 1420 2009-08-22 13:23:16Z vipsoft $
 * 
 * @category Piwik
 * @package Piwik
 */

/**
 * Array of blob records.
 * Useful for easily saving splited data in the DB.
 *  
 * Example: $record = new Piwik_ArchiveProcessing_RecordArray(
 * 				'veryLongBook', 
 * 				0 => serialize(	array( '1st chapter very long, 6MB of data we dont want to save' )),
 * 				1 => serialize(	array( '2nd chapter very long, 8MB of data we dont want to save' )),
 * 				2 => serialize(	array( '3rd chapter very long, 7MB of data we dont want to save' )),
 * 				3 => serialize(	array( '4th chapter very long, 10MB of data we dont want to save' )),
 * 		);
 * 
 * Will be saved in the DB as 
 * 		veryLongBook   => X
 * 		veryLongBook_1 => Y
 * 		veryLongBook_2 => Z
 * 		veryLongBook_3 => M
 * 
 * @package Piwik
 * @subpackage Piwik_ArchiveProcessing
 */
class Piwik_ArchiveProcessing_RecordArray extends Piwik_ArchiveProcessing_Record
{
	protected $records = array();
	
	function __construct( $name, $aValue)
	{		
		foreach($aValue as $id => $value)
		{
			// for the parent Table we keep the name
			// for example for the Table of searchEngines we keep the name 'referer_search_engine'
			// but for the child table of 'Google' which has the ID = 9 the name would be 'referer_search_engine_9'
			if($id == 0)
			{
				$newName = $name;
			}
			else
			{
				$newName = $name . '_' . $id;
			}
			$this->records[] = new Piwik_ArchiveProcessing_Record_Blob( $newName,  $value );
		}
	}

	public function __destruct()
	{
		foreach($this->records as $record)
		{
			destroy($record);
		}
		$this->records = array();
	}

	public function get()
	{
		return $this->records;
	}
}
