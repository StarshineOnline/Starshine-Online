<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: modifier.urlRewriteWithParameters.php 2069 2010-04-09 13:10:37Z matt $
 * 
 * @category Piwik
 * @package SmartyPlugins
 */

/**
 * Rewrites the given URL and modify the given parameters.
 * @see Piwik_Url::getCurrentQueryStringWithParametersModified()
 * 
 * @return string
 */
function smarty_modifier_urlRewriteWithParameters($parameters)
{
	$parameters['updated'] = null;
	$url = Piwik_Url::getCurrentQueryStringWithParametersModified($parameters);
	return htmlspecialchars($url);
}
