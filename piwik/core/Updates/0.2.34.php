<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: 0.2.34.php 1980 2010-03-22 18:46:56Z vipsoft $
 *
 * @category Piwik
 * @package Updates
 */

/**
 * @package Updates
 */
class Piwik_Updates_0_2_34 extends Piwik_Updates
{
	static function update($adapter = 'PDO_MYSQL')
	{
		// force regeneration of cache files following #648
		Piwik::setUserIsSuperUser();
		$allSiteIds = Piwik_SitesManager_API::getInstance()->getAllSitesId();
		Piwik_Common::regenerateCacheWebsiteAttributes($allSiteIds);
	}
}
