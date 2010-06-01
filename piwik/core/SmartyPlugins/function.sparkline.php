<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: function.sparkline.php 1420 2009-08-22 13:23:16Z vipsoft $
 * 
 * @category Piwik
 * @package SmartyPlugins
 */

/**
 * @param string $url
 * @return string IMG HTML tag 
 */
function smarty_function_sparkline($params, &$smarty = false)
{
	$src = $params['src'];
	$width = Piwik_Visualization_Sparkline::getWidth();
	$height = Piwik_Visualization_Sparkline::getHeight();
	return "<img class=\"sparkline\" alt=\"\" src=\"$src\" width=\"$width\" height=\"$height\" />";
}
