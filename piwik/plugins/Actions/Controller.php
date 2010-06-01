<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: Controller.php 2202 2010-05-20 08:49:42Z matt $
 * 
 * @category Piwik_Plugins
 * @package Piwik_Actions
 */

/**
 * Actions controller
 *
 * @package Piwik_Actions
 */
class Piwik_Actions_Controller extends Piwik_Controller 
{
	protected function getPageUrlsView($currentAction, $controllerActionSubtable)
	{
		$view = Piwik_ViewDataTable::factory();
		$view->init(  	$this->pluginName, 
						$currentAction,
						'Actions.getPageUrls',
						$controllerActionSubtable );
		$view->setColumnTranslation('label', Piwik_Translate('Actions_ColumnPageURL'));
		return $view;
	}
	
	public function getPageUrls($fetch = false)
	{
		$view = $this->getPageUrlsView(__FUNCTION__, 'getPageUrlsSubDataTable');
		$this->configureViewPageUrls($view);
		$this->configureViewActions($view);
		return $this->renderView($view, $fetch);
	}
	
	public function getPageUrlsSubDataTable($fetch = false)
	{
		$view = $this->getPageUrlsView(__FUNCTION__, 'getPageUrlsSubDataTable');
		$this->configureViewPageUrls($view);
		$this->configureViewActions($view);
		return $this->renderView($view, $fetch);
	}

	protected function configureViewPageUrls($view)
	{
		$view->setColumnsToDisplay( array('label','nb_hits','nb_visits', 'bounce_rate', 'avg_time_on_page', 'exit_rate') );
	}

	public function getEntryPageUrls($fetch = false)
	{
		$view = $this->getPageUrlsView(__FUNCTION__, 'getEntryPageUrlsSubDataTable');
		$this->configureViewEntryPageUrls($view);
		$this->configureViewActions($view);
		return $this->renderView($view, $fetch);
	}
	
	public function getEntryPageUrlsSubDataTable($fetch = false)
	{
		$view = $this->getPageUrlsView(__FUNCTION__, 'getEntryPageUrlsSubDataTable');
		$this->configureViewEntryPageUrls($view);
		$this->configureViewActions($view);
		return $this->renderView($view, $fetch);
	}
	
	protected function configureViewEntryPageUrls($view)
	{
		$view->setSortedColumn('entry_nb_visits');
		$view->setColumnsToDisplay( array('label','entry_nb_visits', 'entry_bounce_count', 'bounce_rate') );
		$view->setColumnTranslation('entry_bounce_count', Piwik_Translate('General_ColumnBounces'), Piwik_Translate('General_BouncesDefinition'));
		$view->setColumnTranslation('entry_nb_visits', Piwik_Translate('General_ColumnEntrances'), Piwik_Translate('General_EntrancesDefinition'));
		// remove pages that are not entry pages
		$view->queueFilter('ColumnCallbackDeleteRow', array('entry_nb_visits', 'strlen'));
	}

	public function getExitPageUrls($fetch = false)
	{
		$view = $this->getPageUrlsView(__FUNCTION__, 'getExitPageUrlsSubDataTable');
		$this->configureViewExitPageUrls($view);
		$this->configureViewActions($view);
		return $this->renderView($view, $fetch);
	}
	
	public function getExitPageUrlsSubDataTable($fetch = false)
	{
		$view = $this->getPageUrlsView(__FUNCTION__, 'getExitPageUrlsSubDataTable');
		$this->configureViewExitPageUrls($view);
		$this->configureViewActions($view);
		return $this->renderView($view, $fetch);
	}
	
	protected function configureViewExitPageUrls($view)
	{
		$view->setSortedColumn('exit_nb_visits');
		$view->setColumnsToDisplay( array('label', 'exit_nb_visits', 'nb_visits', 'exit_rate') );
		$view->setColumnTranslation('exit_nb_visits', Piwik_Translate('General_ColumnExits'), Piwik_Translate('General_ExitsDefinition'));
		// remove pages that are not exit pages
		$view->queueFilter('ColumnCallbackDeleteRow', array('exit_nb_visits', 'strlen'));
	}
	
	public function getPageTitles($fetch = false)
	{
		$view = Piwik_ViewDataTable::factory();
		$view->init(  	$this->pluginName,
						__FUNCTION__,
						'Actions.getPageTitles',
						'getPageTitlesSubDataTable' );
		$view->setColumnTranslation('label', Piwik_Translate('Actions_ColumnPageName'));
		$this->configureViewPageTitles($view);
		$this->configureViewActions($view);
		return $this->renderView($view, $fetch);
	}

	public function getPageTitlesSubDataTable($fetch = false)
	{
		$view = Piwik_ViewDataTable::factory();
		$view->init(  	$this->pluginName,
						__FUNCTION__,
						'Actions.getPageTitles',
						'getPageTitlesSubDataTable'  );
		$this->configureViewPageTitles($view);
		$this->configureViewActions($view);
		return $this->renderView($view, $fetch);
	}

	protected function configureViewPageTitles($view)
	{
		$view->setColumnsToDisplay( array('label','nb_hits','nb_visits') );
	}
	
	public function getDownloads($fetch = false)
	{
		$view = Piwik_ViewDataTable::factory();
		$view->init(  	$this->pluginName, 
						__FUNCTION__,
						'Actions.getDownloads',
						'getDownloadsSubDataTable' );
		
		$this->configureViewDownloads($view);
		return $this->renderView($view, $fetch);
	}
	
	public function getDownloadsSubDataTable($fetch = false)
	{
		$view = Piwik_ViewDataTable::factory();
		$view->init(  	$this->pluginName, 
						__FUNCTION__,
						'Actions.getDownloads',
						'getDownloadsSubDataTable');
		$this->configureViewDownloads($view);
		return $this->renderView($view, $fetch);
	}

	public function getOutlinks($fetch = false)
	{
		$view = Piwik_ViewDataTable::factory();
		$view->init(  	$this->pluginName, 
						__FUNCTION__,
						'Actions.getOutlinks',
						'getOutlinksSubDataTable' );
		$this->configureViewOutlinks($view);
		return $this->renderView($view, $fetch);
	}
	
	public function getOutlinksSubDataTable($fetch = false)
	{
		$view = Piwik_ViewDataTable::factory();
		$view->init(	$this->pluginName, 
						__FUNCTION__,
						'Actions.getOutlinks',
						'getOutlinksSubDataTable');
		$this->configureViewOutlinks($view);
		return $this->renderView($view, $fetch);
	}

	/*
	 * Page titles & Page URLs reports
	 */
	protected function configureViewActions($view)
	{
		$view->setColumnTranslation('nb_hits', Piwik_Translate('General_ColumnPageviews'));
		$view->setColumnTranslation('nb_visits', Piwik_Translate('General_ColumnUniquePageviews'));
		$view->setColumnTranslation('avg_time_on_page', Piwik_Translate('General_ColumnAverageTimeOnPage'), Piwik_Translate('General_AverageTimeOnPageDefinition'));
		$view->setColumnTranslation('bounce_rate', Piwik_Translate('General_ColumnBounceRate'), Piwik_Translate('General_PageBounceRateDefinition'));
		$view->setColumnTranslation('exit_rate', Piwik_Translate('General_ColumnExitRate'), Piwik_Translate('General_PageExitRateDefinition'));
		$view->queueFilter('ColumnCallbackReplace', array('avg_time_on_page', array('Piwik', 'getPrettyTimeFromSeconds')));
		
		if(Piwik_Common::getRequestVar('enable_filter_excludelowpop', '0', 'string' ) != '0')
		{
			// computing minimum value to exclude
			$visitsInfo = Piwik_VisitsSummary_Controller::getVisitsSummary();
			$visitsInfo = $visitsInfo->getFirstRow();
			$nbActions = $visitsInfo->getColumn('nb_actions');
			$nbActionsLowPopulationThreshold = floor(0.02 * $nbActions); // 2 percent of the total number of actions
			// we remove 1 to make sure some actions/downloads are displayed in the case we have a very few of them
			// and each of them has 1 or 2 hits...
			$nbActionsLowPopulationThreshold = min($visitsInfo->getColumn('max_actions')-1, $nbActionsLowPopulationThreshold-1);
			
			$view->setExcludeLowPopulation( 'nb_hits', $nbActionsLowPopulationThreshold );
		}

		$this->configureGenericViewActions($view);
		return $view;
	}
	
	/*
	 * Downloads report
	 */
	protected function configureViewDownloads($view)
	{
		$view->setColumnsToDisplay( array('label','nb_visits','nb_hits') );
		$view->setColumnTranslation('label', Piwik_Translate('Actions_ColumnDownloadURL'));
		$view->setColumnTranslation('nb_hits', Piwik_Translate('Actions_ColumnDownloads'));
		$view->setColumnTranslation('nb_visits', Piwik_Translate('Actions_ColumnUniqueDownloads'));
		$view->disableExcludeLowPopulation();
		$this->configureGenericViewActions($view);
	}
	
	/*
	 * Outlinks report
	 */
	protected function configureViewOutlinks($view)
	{
		$view->setColumnsToDisplay( array('label','nb_visits','nb_hits') );
		$view->setColumnTranslation('label', Piwik_Translate('Actions_ColumnClickedURL'));
		$view->setColumnTranslation('nb_hits', Piwik_Translate('Actions_ColumnClicks'));
		$view->setColumnTranslation('nb_visits', Piwik_Translate('Actions_ColumnUniqueClicks'));
		$view->disableExcludeLowPopulation();
		$this->configureGenericViewActions($view);
	}

	/*
	 * Common to all Actions reports, how to use the custom Actions Datatable html  
	 */
	protected function configureGenericViewActions($view)
	{
		$view->setTemplate('CoreHome/templates/datatable_actions.tpl');
		if(Piwik_Common::getRequestVar('idSubtable', -1) != -1)
		{
			$view->setTemplate('CoreHome/templates/datatable_actions_subdatable.tpl');
		}
		$currentlySearching = $view->setSearchRecursive();
		if($currentlySearching)
		{
			$view->setTemplate('CoreHome/templates/datatable_actions_recursive.tpl');
		}
		// disable Footer icons
		$view->disableOffsetInformation();
		$view->disableShowAllViewsIcons();
		$view->disableShowAllColumns();
		
		$view->setLimit( 100 );
		$view->main();
		// we need to rewrite the phpArray so it contains all the recursive arrays
		if($currentlySearching)
		{
			$phpArrayRecursive = $this->getArrayFromRecursiveDataTable($view->getDataTable());
			$view->getView()->arrayDataTable = $phpArrayRecursive;
		}
	}
	
	protected function getArrayFromRecursiveDataTable( $dataTable, $depth = 0 )
	{
		$table = array();
		foreach($dataTable->getRows() as $row)
		{
			$phpArray = array();
			if(($idSubtable = $row->getIdSubDataTable()) !== null)
			{
				$subTable = Piwik_DataTable_Manager::getInstance()->getTable( $idSubtable );
					
				if($subTable->getRowsCount() > 0)
				{
					$phpArray = $this->getArrayFromRecursiveDataTable( $subTable, $depth + 1 );
				}
			}
			
			$label = $row->getColumn('label');
			$newRow = array(
				'level' => $depth,
				'columns' => $row->getColumns(),
				'metadata' => $row->getMetadata(),
				'idsubdatatable' => $row->getIdSubDataTable()
				);
			$table[] = $newRow;
			if(count($phpArray) > 0)
			{
				$table = array_merge( $table,  $phpArray);
			}
		}
		return $table;
	}
}
