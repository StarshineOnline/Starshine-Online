<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: Goals.php 2020 2010-03-30 12:36:04Z matt $
 * 
 * @category Piwik
 * @package Piwik
 */

/**
 * @package Piwik
 * @subpackage Piwik_ViewDataTable
 */
class Piwik_ViewDataTable_HtmlTable_Goals extends Piwik_ViewDataTable_HtmlTable 
{
	protected function getViewDataTableId()
	{
		return 'tableGoals';
	}
	
	public function main()
	{
		$this->idSite = Piwik_Common::getRequestVar('idSite', null, 'int');
		$this->viewProperties['show_exclude_low_population'] = true;
		$this->viewProperties['show_goals'] = true;
		$this->setColumnsToDisplay( array(	'label', 
											'nb_visits', 
											'goals_conversion_rate',
											'goal_%s_conversion_rate',
											'revenue_per_visit',
							));
		parent::main();
	}
	
	public function disableSubTableWhenShowGoals()
	{
		$this->controllerActionCalledWhenRequestSubTable = null;
	}
	
	protected function getRequestString()
	{
		$requestString = parent::getRequestString();
		return $requestString . '&filter_update_columns_when_show_all_goals=1';
	}
	
	protected $columnsToPercentageFilter = array();

	private function getIdSite()
	{
		return $this->idSite;
	}
	
	public function setColumnsToDisplay($columnsNames)
	{
		$newColumnsNames = array();
		foreach($columnsNames as $columnName)
		{
			if($columnName == 'goal_%s_conversion_rate')
			{
				$goals = Piwik_Goals_API::getInstance()->getGoals( $this->getIdSite() );
				foreach($goals as $goal)
				{
					$idgoal = $goal['idgoal'];
					$name = $goal['name'];
					$columnName = 'goal_'.$idgoal.'_conversion_rate';
					$newColumnsNames[] = $columnName;
					$this->setColumnTranslation($columnName, $name);
					$this->columnsToPercentageFilter[] = $columnName;
				}
			}
			else
			{
				$newColumnsNames[] = $columnName;
			}
		}
		parent::setColumnsToDisplay($newColumnsNames);
	}
	
	protected function postDataTableLoadedFromAPI()
	{
		parent::postDataTableLoadedFromAPI();
		$this->columnsToPercentageFilter[] = 'goals_conversion_rate';
		foreach($this->columnsToPercentageFilter as $columnName)
		{
			$this->dataTable->filter('ColumnCallbackReplace', array($columnName, create_function('$rate', 'return $rate."%";')));
		}
		$this->dataTable->filter('ColumnCallbackReplace', array('revenue_per_visit', create_function('$value', 'return sprintf("%.1f",$value);')));
		$this->dataTable->filter('ColumnCallbackReplace', array('revenue_per_visit', array("Piwik", "getPrettyMoney"), array($this->getIdSite())));
	}
}
