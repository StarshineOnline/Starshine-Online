{assign var=showSitesSelection value=true}
{assign var=showPeriodSelection value=false}
{assign var=showMenu value=false}
{include file="CoreAdminHome/templates/header.tpl"}

<style>
{fetch file="plugins/API/templates/styles.css"}
</style>

{'API_QuickDocumentation'|translate:$token_auth}
<span id='token_auth'>token_auth = <b>{$token_auth}</b></span>
<p><i>{'API_LoadedAPIs'|translate:$countLoadedAPI}</i></p>
{$list_api_methods_with_links}
