<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: 0.4.1.php 1980 2010-03-22 18:46:56Z vipsoft $
 *
 * @category Piwik
 * @package Updates
 */

/**
 * @package Updates
 */
class Piwik_Updates_0_4_1 extends Piwik_Updates
{
	static function getSql($adapter = 'PDO_MYSQL')
	{
		return array(
			'ALTER TABLE `'. Piwik::prefixTable('log_conversion') .'`
				CHANGE `idlink_va` `idlink_va` INT(11) DEFAULT NULL' => false,
			'ALTER TABLE `'. Piwik::prefixTable('log_conversion') .'`
				CHANGE `idaction` `idaction` INT(11) DEFAULT NULL' => '1054',
		);
	}

	static function update()
	{
		Piwik_Updater::updateDatabase(__FILE__, self::getSql());
	}
}
