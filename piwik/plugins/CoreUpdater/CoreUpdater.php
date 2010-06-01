<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: CoreUpdater.php 1969 2010-03-21 23:34:22Z vipsoft $
 * 
 * @category Piwik_Plugins
 * @package Piwik_CoreUpdater
 */

/**
 *
 * @package Piwik_CoreUpdater
 */
class Piwik_CoreUpdater extends Piwik_Plugin
{
	public function getInformation()
	{
		return array(
			'name' => 'CoreUpdater',
			'description' => Piwik_Translate('CoreUpdater_PluginDescription'),
			'author' => 'Piwik',
			'author_homepage' => 'http://piwik.org/',
			'version' => Piwik_Version::VERSION,
		);
	}

	function getListHooksRegistered()
	{
		$hooks = array(
			'FrontController.dispatchCoreAndPluginUpdatesScreen' => 'dispatch',
			'FrontController.checkForUpdates' => 'updateCheck',
		);
		return $hooks;
	}

	public static function getComponentUpdates($updater)
	{
		$updater->addComponentToCheck('core', Piwik_Version::VERSION);

		$plugins = Piwik_PluginsManager::getInstance()->getLoadedPlugins();
		foreach($plugins as $pluginName => $plugin)
		{
			$updater->addComponentToCheck($pluginName, $plugin->getVersion());
		}
		
		$componentsWithUpdateFile = $updater->getComponentsWithUpdateFile();
		if(count($componentsWithUpdateFile) == 0 && !$updater->hasNewVersion('core'))
		{
			return null;
		}

		return $componentsWithUpdateFile;
	}

	function dispatch()
	{
		$module = Piwik_Common::getRequestVar('module', '', 'string');
		$updater = new Piwik_Updater();
		if(self::getComponentUpdates($updater) !== null && $module != 'CoreUpdater')
		{
			Piwik::redirectToModule('CoreUpdater');
		}
	}

	function updateCheck()
	{
		Piwik_UpdateCheck::check();
	}
}
