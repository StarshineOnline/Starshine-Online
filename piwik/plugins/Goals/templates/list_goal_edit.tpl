
<span id='EditGoals' style="display:none;">
	<table class="tableForm">
	<thead style="font-weight:bold">
		<td>Id</td>
        <td>{'Goals_GoalName'|translate}</td>
        <td>{'Goals_GoalIsTriggeredWhen'|translate}</td>
        <td>{'Goals_ColumnRevenue'|translate}</td>
        <td>{'General_Edit'|translate}</td>
        <td>{'General_Delete'|translate}</td>
	</thead>
	{foreach from=$goals item=goal}
	<tr>
		<td>{$goal.idgoal}</td>
		<td>{$goal.name}</td>
        <td><span class='matchAttribute'>{$goal.match_attribute}</span> {if isset($goal.pattern_type)}<br />{'Goals_Pattern'|translate} {$goal.pattern_type}: {$goal.pattern}</b>{/if}</td>
		<td>{if $goal.revenue==0}-{else}{$goal.revenue|money:$idSite}{/if}</td>
		<td><a href='#' name="linkEditGoal" id="{$goal.idgoal}"><img src='plugins/UsersManager/images/edit.png' border="0" /> {'General_Edit'|translate}</a></td>
		<td><a href='#' name="linkDeleteGoal" id="{$goal.idgoal}"><img src='plugins/UsersManager/images/remove.png' border="0" /> {'General_Delete'|translate}</a></td>
	</tr>
	{/foreach}
	</table>
</span>

<script type="text/javascript">
var goalTypeToTranslation = {ldelim}
    "manually" : "{'Goals_ManuallyTriggeredUsingJavascriptFunction'|translate}",
    "file" : "{'Goals_Download'|translate}",
    "url" : "{'Goals_VisitUrl'|translate}",
    "external_website" : "{'Goals_ClickOutlink'|translate}"
{rdelim}
{literal}
$(document).ready( function() {	
	// translation of the goal "match attribute" to human readable description
	$('.matchAttribute').each( function() {
		matchAttribute = $(this).text();
		translation = goalTypeToTranslation[matchAttribute];
		$(this).text(translation);
	});
} );
{/literal}
</script>
