<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Piwik &rsaquo; {'CoreAdminHome_Administration'|translate}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="Piwik {$piwik_version}" />
<link rel="shortcut icon" href="plugins/CoreHome/templates/images/favicon.ico" />

{include file="CoreHome/templates/js_global_variables.tpl"}

<link rel="stylesheet" type="text/css" href="themes/default/common.css" />
<link rel="stylesheet" type="text/css" href="libs/jquery/themes/base/jquery-ui.css" class="ui-theme" />
<link rel="stylesheet" type="text/css" href="plugins/CoreAdminHome/templates/styles.css" />
{postEvent name="template_css_import"}

<script type="text/javascript" src="libs/jquery/jquery.js"></script>
<script type="text/javascript" src="libs/jquery/jquery-ui.js"></script>
<script type="text/javascript" src="libs/jquery/jquery.bgiframe.js"></script>
<script type="text/javascript" src="libs/jquery/jquery.blockUI.js"></script>
<script type="text/javascript" src="libs/jquery/fdd2div-modified.js"></script>
<script type="text/javascript" src="libs/javascript/sprintf.js"></script>
<script type="text/javascript" src="themes/default/common.js"></script>

<script type="text/javascript" src="libs/jquery/jquery.history.js"></script>
<script type="text/javascript" src="plugins/CoreHome/templates/broadcast.js"></script>
{postEvent name="template_js_import"}

</head>
<body>
{include file="CoreHome/templates/top_bar.tpl"}

<div id="header">
{include file="CoreHome/templates/header_message.tpl"}
{include file="CoreHome/templates/logo.tpl"}
{if $showPeriodSelection}{include file="CoreHome/templates/period_select.tpl"}{/if}
{include file="CoreHome/templates/js_disabled_notice.tpl"}
</div>

<br class="clearAll" />

<div id="content">

{ajaxRequestErrorDiv}
{if !isset($showMenu) || $showMenu}
	{include file="CoreAdminHome/templates/menu.tpl"}
{/if}

{if !empty($configFileNotWritable)}
<div class="ajaxSuccess" style="display:normal">
	<p>{'General_ConfigFileIsNotWritable'|translate:"(config/config.ini.php)":"<br/>"}</p>
</div>
{elseif strpos($url, 'updated=1')}	
<div class="ajaxSuccess" style="display:normal">
	<p>{'General_YourChangesHaveBeenSaved'|translate}</p>
</div>
{/if}

