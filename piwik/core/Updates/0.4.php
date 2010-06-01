<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: 0.4.php 2192 2010-05-17 05:30:15Z vipsoft $
 *
 * @category Piwik
 * @package Updates
 */

/**
 * @package Updates
 */
class Piwik_Updates_0_4 extends Piwik_Updates
{
	static function getSql($adapter = 'PDO_MYSQL')
	{
		return array(
			// 0.4 [1140]
			'UPDATE `'. Piwik::prefixTable('log_visit') .'`
				SET location_ip=location_ip+CAST(POW(2,32) AS UNSIGNED) WHERE location_ip < 0' => false,
			'ALTER TABLE `'. Piwik::prefixTable('log_visit') .'`
				CHANGE `location_ip` `location_ip` BIGINT UNSIGNED NOT NULL' => false,
			'UPDATE `'. Piwik::prefixTable('logger_api_call') .'`
				SET caller_ip=caller_ip+CAST(POW(2,32) AS UNSIGNED) WHERE caller_ip < 0' => false,
			'ALTER TABLE `'. Piwik::prefixTable('logger_api_call') .'`
				CHANGE `caller_ip` `caller_ip` BIGINT UNSIGNED' => false,
		);
	}

	static function update()
	{
		Piwik_Updater::updateDatabase(__FILE__, self::getSql());
	}
}
