<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: function.url.php 1420 2009-08-22 13:23:16Z vipsoft $
 * 
 * @category Piwik
 * @package SmartyPlugins
 */

/**
 * Smarty {url} function plugin.
 * Generates a piwik URL with the specified parameters modified.
 *
 * Examples:
 * <pre>
 * {url module="API"} will rewrite the URL modifying the module GET parameter
 * {url module="API" method="getKeywords"} will rewrite the URL modifying the parameters module=API method=getKeywords
 * </pre>
 * 
 * @see Piwik_Url::getCurrentQueryStringWithParametersModified()
 * @param $name=$value of the parameters to modify in the generated URL
 * @return	string Something like index.php?module=X&action=Y 
 */
function smarty_function_url($params, &$smarty)
{
	return htmlspecialchars('index.php' . Piwik_Url::getCurrentQueryStringWithParametersModified( $params ));
}
