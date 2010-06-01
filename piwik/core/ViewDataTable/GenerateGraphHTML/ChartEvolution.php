<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: ChartEvolution.php 1796 2010-01-22 14:43:11Z vipsoft $
 * 
 * @category Piwik
 * @package Piwik
 */

/**
 * Generates HTML embed for the Evolution graph
 *  
 * @package Piwik
 * @subpackage Piwik_ViewDataTable
 */
class Piwik_ViewDataTable_GenerateGraphHTML_ChartEvolution extends Piwik_ViewDataTable_GenerateGraphHTML
{
	function __construct()
	{
		$this->width='100%';
		$this->height=150;
		// used for the CSS class to apply to the DIV containing the graph
		$this->graphType = 'evolution';		
	}

	protected function getViewDataTableId()
	{
		return 'graphEvolution';
	}
	
	protected function getViewDataTableIdToLoad()
	{
		return 'generateDataChartEvolution';
	}
	
	function init($currentControllerName,
						$currentControllerAction, 
						$apiMethodToRequestDataTable,
						$controllerActionCalledWhenRequestSubTable = null)
	{
		parent::init($currentControllerName,
						$currentControllerAction, 
						$apiMethodToRequestDataTable,
						$controllerActionCalledWhenRequestSubTable);
		
		$this->setParametersToModify(array('date' => Piwik_Common::getRequestVar('date', 'last30', 'string')));
		$this->disableShowAllViewsIcons();
		$this->disableShowTable();
	}
	
	/**
	 * Sets the columns that will be displayed on output evolution chart
	 * By default all columns are displayed ($columnsNames = array() will display all columns)
	 * 
	 * @param array $columnsNames Array of column names eg. array('nb_visits','nb_hits')
	 */
	public function setColumnsToDisplay( $columnsNames)
	{
		if(!is_array($columnsNames)) 
		{
			$columnsNames = array($columnsNames);
		}
		$this->setParametersToModify( array('columns' => $columnsNames) );
	}
}
