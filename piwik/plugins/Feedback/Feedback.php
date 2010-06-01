<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: Feedback.php 1920 2010-03-16 03:13:33Z vipsoft $
 *
 * @category Piwik_Plugins
 * @package Piwik_Feedback
 */

/**
 *
 * @package Piwik_Feedback
 */
class Piwik_Feedback extends Piwik_Plugin
{
	public function getInformation()
	{
		return array(
			'name' => 'Feedback',
			'description' => Piwik_Translate('Feedback_PluginDescription'),
			'author' => 'Piwik',
			'author_homepage' => 'http://piwik.org/',
			'version' => Piwik_Version::VERSION,
		);
	}

	function getListHooksRegistered()
	{
		return array(
			'template_css_import' => 'css',
			'template_js_import' => 'js',
		);
	}

	function css()
	{
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"plugins/Feedback/templates/styles.css\" />\n";
	}

	function js()
	{
		echo "<script type=\"text/javascript\" src=\"plugins/Feedback/templates/feedback.js\"></script>\n";
	}
}
