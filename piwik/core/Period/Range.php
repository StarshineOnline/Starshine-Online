<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: Range.php 2067 2010-04-09 09:18:01Z matt $
 * 
 * @category Piwik
 * @package Piwik
 */

/**
 * from a starting date to an ending date
 *
 * @package Piwik
 * @subpackage Piwik_Period
 */
class Piwik_Period_Range extends Piwik_Period
{
	public function __construct( $strPeriod, $strDate, $timezone = 'UTC' )
	{
		$this->strPeriod = $strPeriod;
		$this->strDate = $strDate;
		$this->defaultEndDate = null;
		$this->timezone = $timezone;
	}
	public function getLocalizedShortString()
	{
		//"30 Dec 08 - 26 Feb 09"
		$dateStart = $this->getDateStart();
		$dateEnd = $this->getDateEnd();
		$template = "%day% %shortMonth% %shortYear%";
		$shortDateStart = $dateStart->getLocalized($template);
		$shortDateEnd = $dateEnd->getLocalized($template);
		$out = "$shortDateStart - $shortDateEnd";
		return $out;
	}

	public function getLocalizedLongString()
	{
		return $this->getLocalizedShortString();
	}
	public function getPrettyString()
	{
		$out = "From ".$this->getDateStart()->toString() . " to " . $this->getDateEnd()->toString();
		return $out;
	}

	/**
	 *
	 * @param Piwik_Date $date
	 * @param int $n
	 * @return Piwik_Date
	 */
	protected function removePeriod( $date, $n )
	{
		switch($this->strPeriod)
		{
			case 'day':	
				$startDate = $date->subDay( $n );
			break;
			
			case 'week':
				$startDate = $date->subDay( $n * 7 );					
			break;
			
			case 'month':
				$startDate = $date->subMonth( $n );					
			break;
			
			case 'year':
				$startDate = $date->subMonth( 12 * $n );					
			break;
			
			default:
				throw new Exception(sprintf(self::$unknowPeriodException, $this->strPeriod));
			break;
		}
		return $startDate;
	}

	protected function getMaxN($lastN)
	{	
		switch($this->strPeriod)
		{
			case 'day':	
				$lastN = min( $lastN, 5*365 );
			break;
			
			case 'week':
				$lastN = min( $lastN, 5*52 );				
			break;
			
			case 'month':
				$lastN = min( $lastN, 5*12 );			
			break;
			
			case 'year':
				$lastN = min( $lastN, 10 );					
			break;
		}
		return $lastN;
	}
	
	public function setDefaultEndDate( Piwik_Date $oDate)
	{
		$this->defaultEndDate = $oDate;
	}
	
	protected function generate()
	{
		if($this->subperiodsProcessed)
		{
			return;
		}
		parent::generate();
		
		if(preg_match('/(last|previous)([0-9]*)/', $this->strDate, $regs))
		{
			$lastN = $regs[2];
			$lastOrPrevious = $regs[1];
			if(!is_null($this->defaultEndDate))
			{
				$defaultEndDate = $this->defaultEndDate;
			}
			else
			{
				$defaultEndDate = Piwik_Date::factory('now', $this->timezone);
			}		
			if($lastOrPrevious == 'last')
			{
				$endDate = $defaultEndDate;
			}
			elseif($lastOrPrevious == 'previous')
			{
				$endDate = $this->removePeriod($defaultEndDate, 1);
			}		
			
			// last1 means only one result ; last2 means 2 results so we remove only 1 to the days/weeks/etc
			$lastN--;
			$lastN = abs($lastN);
			
			$lastN = $this->getMaxN($lastN);
			
			$startDate = $this->removePeriod($endDate, $lastN);
		}
		elseif(preg_match('/([0-9]{4}-[0-9]{1,2}-[0-9]{1,2}),([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})/', $this->strDate, $regs))
		{
			$strDateStart = $regs[1];
			$strDateEnd = $regs[2];

			$startDate = Piwik_Date::factory($strDateStart);
			$endDate   = Piwik_Date::factory($strDateEnd);
		}
		else
		{
			throw new Exception("The date '$this->strDate' is not a date range. Should have the following format: 'lastN' or 'previousN' or 'YYYY-MM-DD,YYYY-MM-DD'.");
		}
		$endSubperiod = Piwik_Period::factory($this->strPeriod, $endDate);
		
		$arrayPeriods= array();
		$arrayPeriods[] = $endSubperiod;
		while($endDate->isLater($startDate) )
		{
			$endDate = $this->removePeriod($endDate, 1);
			$subPeriod = Piwik_Period::factory($this->strPeriod, $endDate);
			$arrayPeriods[] =  $subPeriod ;
		}
		$arrayPeriods = array_reverse($arrayPeriods);
		foreach($arrayPeriods as $period)
		{
			$this->addSubperiod($period);
		}
	}
	
	function toString()
	{
		if(!$this->subperiodsProcessed)
		{
			$this->generate();
		}
		$range = array();
		foreach($this->subperiods as $element)
		{
			$range[] = $element->toString();
		}
		return $range;
	}
}
