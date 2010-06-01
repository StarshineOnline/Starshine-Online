<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: API.php 1816 2010-01-29 21:27:47Z vipsoft $
 * 
 * @category Piwik_Plugins
 * @package Piwik_API
 */

/**
 * 
 * @package Piwik_API
 */
class Piwik_API extends Piwik_Plugin
{
	public function getInformation()
	{
		return array(
			'name' => 'API',
			'description' => Piwik_Translate('API_PluginDescription'),
			'homepage' => 'misc/redirectToUrl.php?url=http://dev.piwik.org/trac/wiki/API/Reference',
			'author' => 'Piwik',
			'author_homepage' => 'http://piwik.org/',
			'version' => Piwik_Version::VERSION,
		);
	}
}
