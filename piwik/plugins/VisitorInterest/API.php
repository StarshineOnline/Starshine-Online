<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: API.php 1832 2010-02-10 08:14:15Z vipsoft $
 * 
 * @category Piwik_Plugins
 * @package Piwik_VisitorInterest
 */

/**
 *
 * @package Piwik_VisitorInterest
 */
class Piwik_VisitorInterest_API 
{
	static private $instance = null;
	static public function getInstance()
	{
		if (self::$instance == null)
		{            
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}

	protected function getDataTable($name, $idSite, $period, $date)
	{
		Piwik::checkUserHasViewAccess( $idSite );
		$archive = Piwik_Archive::build($idSite, $period, $date );
		$dataTable = $archive->getDataTable($name);
		$dataTable->filter('Sort',array(Piwik_Archive::INDEX_NB_VISITS));
		$dataTable->queueFilter('ReplaceColumnNames');
		$dataTable->queueFilter('Sort', array('label', 'asc', true));
		return $dataTable;
	}
	
	public function getNumberOfVisitsPerVisitDuration( $idSite, $period, $date )
	{
		$dataTable = $this->getDataTable('VisitorInterest_timeGap', $idSite, $period, $date);
		$dataTable->queueFilter('ColumnCallbackReplace', array('label', 'Piwik_getDurationLabel'));
		return $dataTable;
	}

	public function getNumberOfVisitsPerPage( $idSite, $period, $date )
	{
		$dataTable = $this->getDataTable('VisitorInterest_pageGap', $idSite, $period, $date);
		$dataTable->queueFilter('ColumnCallbackReplace', array('label', 'Piwik_getPageGapLabel'));
		return $dataTable;
	}
}

function Piwik_getDurationLabel($label)
{ 
	if(($pos = strpos($label,'-')) !== false)
	{
		$min = substr($label, 0, $pos);
		$max = substr($label, $pos+1);
		
		if($min == 0 || $min == 30)
		{
			$XYSeconds = Piwik_Translate('VisitorInterest_BetweenXYSeconds');
			return sprintf($XYSeconds, $min, $max);
		}
		else
		{
			$min = $min / 60;
			$max = $max / 60;
			$XYMin = Piwik_Translate('VisitorInterest_BetweenXYMinutes');
			return sprintf($XYMin, $min, $max);
		}
	}
	if(!is_numeric($label))
	{
		return $label;
	}
	$time = intval($label) / 60;
	$plusXMin = Piwik_Translate('VisitorInterest_PlusXMin');
	return sprintf($plusXMin, $time . urlencode('+'));
}

function Piwik_getPageGapLabel($label)
{
	$return = false;
	if(($pos = strpos($label,'-')) !== false)
	{
		$min = substr($label, 0, $pos);
		$max = substr($label, $pos+1);
		
		if($min == $max)
		{
			$return = $min;
		}
	}
	if(!$return)
	{
		$return = $label;
	}
	
	if($return == 1)
	{
		return Piwik_Translate('VisitorInterest_OnePage');
	}

	return sprintf(Piwik_Translate('VisitorInterest_NPages'), $return);
}
