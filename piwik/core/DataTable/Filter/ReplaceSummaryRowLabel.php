<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: ReplaceSummaryRowLabel.php 1420 2009-08-22 13:23:16Z vipsoft $
 * 
 * @category Piwik
 * @package Piwik
 */

/**
 * 
 * @package Piwik
 * @subpackage Piwik_DataTable
 */
class Piwik_DataTable_Filter_ReplaceSummaryRowLabel extends Piwik_DataTable_Filter
{
	public function __construct( $table, $newLabel = null)
	{
		parent::__construct($table);
		if(is_null($newLabel))
		{
			$newLabel = Piwik_Translate('General_Others');
		}
		$this->newLabel = $newLabel;
		$this->filter();
	}
	
	protected function filter()
	{
		$rows = $this->table->getRows();
		foreach($rows as $row)
		{
			if($row->getColumn('label') == Piwik_DataTable::LABEL_SUMMARY_ROW)
			{
				$row->setColumn('label', $this->newLabel);
				break;
			}
		}
	}
}
