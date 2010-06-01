<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: UserSettings.php 1859 2010-02-23 06:35:20Z vipsoft $
 * 
 * @category Piwik_Plugins
 * @package Piwik_UserSettings
 */

/**
 *
 * @package Piwik_UserSettings
 */
class Piwik_UserSettings extends Piwik_Plugin
{	
	public function getInformation()
	{
		$info = array(
			'name' => 'UserSettings',
			'description' => Piwik_Translate('UserSettings_PluginDescription'),
			'author' => 'Piwik',
			'author_homepage' => 'http://piwik.org/',
			'version' => Piwik_Version::VERSION,
		);
		
		return $info;
	}

	static public $browserType_display = array(
		'ie'     => 'Trident (Internet Explorer)',
		'gecko'  => 'Gecko (Firefox, SeaMonkey)',
		'khtml'  => 'KHTML (Konqueror)',
		'webkit' => 'WebKit (Safari, Chrome)',
		'opera'  => 'Presto (Opera)',
	);

	function getListHooksRegistered()
	{
		$hooks = array(
			'ArchiveProcessing_Day.compute' => 'archiveDay',
			'ArchiveProcessing_Period.compute' => 'archivePeriod',
			'WidgetsList.add' => 'addWidgets',
			'Menu.add' => 'addMenu',
		);
		return $hooks;
	}
	
	function addWidgets()
	{
		Piwik_AddWidget( 'UserSettings_VisitorSettings', 'UserSettings_WidgetResolutions', 'UserSettings', 'getResolution');
		Piwik_AddWidget( 'UserSettings_VisitorSettings', 'UserSettings_WidgetBrowsers', 'UserSettings', 'getBrowser');
		Piwik_AddWidget( 'UserSettings_VisitorSettings', 'UserSettings_WidgetPlugins', 'UserSettings', 'getPlugin');
		Piwik_AddWidget( 'UserSettings_VisitorSettings', 'UserSettings_WidgetWidescreen', 'UserSettings', 'getWideScreen');
		Piwik_AddWidget( 'UserSettings_VisitorSettings', 'UserSettings_WidgetBrowserFamilies', 'UserSettings', 'getBrowserType');
		Piwik_AddWidget( 'UserSettings_VisitorSettings', 'UserSettings_WidgetOperatingSystems', 'UserSettings', 'getOS');
		Piwik_AddWidget( 'UserSettings_VisitorSettings', 'UserSettings_WidgetGlobalVisitors', 'UserSettings', 'getConfiguration');
	}
	
	function addMenu()
	{
		Piwik_AddMenu('General_Visitors', 'UserSettings_SubmenuSettings', array('module' => 'UserSettings'));
	}
	
	function archiveDay( $notification )
	{
		require_once PIWIK_INCLUDE_PATH . '/plugins/UserSettings/functions.php';
		
		$archiveProcessing = $notification->getNotificationObject();
		$this->archiveProcessing = $archiveProcessing;
			
		$recordName = 'UserSettings_configuration';
		$labelSQL = "CONCAT(config_os, ';', config_browser_name, ';', config_resolution)";
		$interestByConfiguration = $archiveProcessing->getArrayInterestForLabel($labelSQL);
		$tableConfiguration = $archiveProcessing->getDataTableFromArray($interestByConfiguration);
		$archiveProcessing->insertBlobRecord($recordName, $tableConfiguration->getSerialized());
		destroy($tableConfiguration);
		
		$recordName = 'UserSettings_os';
		$labelSQL = "config_os";
		$interestByOs = $archiveProcessing->getArrayInterestForLabel($labelSQL);
		$tableOs = $archiveProcessing->getDataTableFromArray($interestByOs);
		$archiveProcessing->insertBlobRecord($recordName, $tableOs->getSerialized());
		destroy($tableOs);
		
		$recordName = 'UserSettings_browser';
		$labelSQL = "CONCAT(config_browser_name, ';', config_browser_version)";
		$interestByBrowser = $archiveProcessing->getArrayInterestForLabel($labelSQL);
		$tableBrowser = $archiveProcessing->getDataTableFromArray($interestByBrowser);
		$archiveProcessing->insertBlobRecord($recordName, $tableBrowser->getSerialized());
		
		$recordName = 'UserSettings_browserType';
		$tableBrowserType = $this->getTableBrowserByType($tableBrowser);
		$archiveProcessing->insertBlobRecord($recordName, $tableBrowserType->getSerialized());
		destroy($tableBrowser);
		destroy($tableBrowserType);
		
		$recordName = 'UserSettings_resolution';
		$labelSQL = "config_resolution";
		$interestByResolution = $archiveProcessing->getArrayInterestForLabel($labelSQL);
		$tableResolution = $archiveProcessing->getDataTableFromArray($interestByResolution);
		$tableResolution->filter('ColumnCallbackDeleteRow', array('label', 'Piwik_UserSettings_keepStrlenGreater'));
		$archiveProcessing->insertBlobRecord($recordName, $tableResolution->getSerialized());
		
		$recordName = 'UserSettings_wideScreen';
		$tableWideScreen = $this->getTableWideScreen($tableResolution);
		$archiveProcessing->insertBlobRecord($recordName, $tableWideScreen->getSerialized());
		destroy($tableResolution);
		destroy($tableWideScreen);
		
		$recordName = 'UserSettings_plugin';
		$tablePlugin = $this->getDataTablePlugin();
		$archiveProcessing->insertBlobRecord($recordName, $tablePlugin->getSerialized());
		destroy($tablePlugin);
	}
	
	function archivePeriod( $notification )
	{
		$archiveProcessing = $notification->getNotificationObject();
		
		$dataTableToSum = array( 
				'UserSettings_configuration',
				'UserSettings_os',
				'UserSettings_browser',
				'UserSettings_browserType',
				'UserSettings_resolution',
				'UserSettings_wideScreen',
				'UserSettings_plugin',
		);
		
		$archiveProcessing->archiveDataTable($dataTableToSum);
	}
	
	protected function getTableWideScreen($tableResolution)
	{
		$nameToRow = array();
		foreach($tableResolution->getRows() as $row)
		{
			$resolution = $row->getColumn('label');
			$name = Piwik_getScreenTypeFromResolution($resolution);
			if(!isset($nameToRow[$name]))
			{
				$nameToRow[$name] = new Piwik_DataTable_Row();
				$nameToRow[$name]->addColumn('label', $name);
			}
			
			$nameToRow[$name]->sumRow( $row );
		}
		$tableWideScreen = new Piwik_DataTable();
		$tableWideScreen->addRowsFromArray($nameToRow);
		
		return $tableWideScreen;
	}
	
	protected function getTableBrowserByType($tableBrowser)
	{		
		$nameToRow = array();
		foreach($tableBrowser->getRows() as $row)
		{
			$browserLabel = $row->getColumn('label');
			$familyNameToUse = Piwik_getBrowserFamily($browserLabel);
			if(!isset($nameToRow[$familyNameToUse]))
			{
				$nameToRow[$familyNameToUse] = new Piwik_DataTable_Row();
				$nameToRow[$familyNameToUse]->addColumn('label',$familyNameToUse);
			}
			$nameToRow[$familyNameToUse]->sumRow( $row );
		}
		
		$tableBrowserType = new Piwik_DataTable();
		$tableBrowserType->addRowsFromArray($nameToRow);
		return $tableBrowserType;
	}
	
	protected function getDataTablePlugin()
	{
		$toSelect = "sum(case config_pdf when 1 then 1 else 0 end) as pdf, 
							sum(case config_flash when 1 then 1 else 0 end) as flash, 
							sum(case config_java when 1 then 1 else 0 end) as java, 
							sum(case config_director when 1 then 1 else 0 end) as director,
							sum(case config_quicktime when 1 then 1 else 0 end) as quicktime,
							sum(case config_realplayer when 1 then 1 else 0 end) as realplayer,
							sum(case config_windowsmedia when 1 then 1 else 0 end) as windowsmedia,
							sum(case config_gears when 1 then 1 else 0 end) as gears,
							sum(case config_silverlight when 1 then 1 else 0 end) as silverlight,
							sum(case config_cookie when 1 then 1 else 0 end) as cookie	";
		return $this->archiveProcessing->getSimpleDataTableFromSelect($toSelect, Piwik_Archive::INDEX_NB_VISITS);
	}
}
