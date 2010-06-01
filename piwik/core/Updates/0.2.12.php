<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: 0.2.12.php 1980 2010-03-22 18:46:56Z vipsoft $
 *
 * @category Piwik
 * @package Updates
 */

/**
 * @package Updates
 */
class Piwik_Updates_0_2_12 extends Piwik_Updates
{
	static function getSql($adapter = 'PDO_MYSQL')
	{
		return array(
			'ALTER TABLE `'. Piwik::prefixTable('site') .'`
				CHANGE `ts_created` `ts_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL' => false,
			'ALTER TABLE `'. Piwik::prefixTable('log_visit') .'`
				DROP `config_color_depth`' => false,

			// 0.2.12 [673]
			// Note: requires INDEX privilege
			'DROP INDEX index_idaction ON `'. Piwik::prefixTable('log_action') .'`' => '1091',
		);
	}

	static function update()
	{
		Piwik_Updater::updateDatabase(__FILE__, self::getSql());
	}
}
