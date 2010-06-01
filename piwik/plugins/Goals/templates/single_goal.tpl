{include file="Goals/templates/title_and_evolution_graph.tpl"}

<div style="clear:both;"></div>
{if $nb_conversions > 0}
    <h2>{'Goals_ConversionsOverview'|translate}</h2>
	<ul class="ulGoalTopElements">
    <li>{'Goals_BestCountries'|translate} {include file='Goals/templates/list_top_segment.tpl' topSegment=$topSegments.country}</li>
    {if count($topSegments.keyword)>0}<li>{'Goals_BestKeywords'|translate} {include file='Goals/templates/list_top_segment.tpl' topSegment=$topSegments.keyword}</li>{/if}
    {if count($topSegments.website)>0}<li>{'Goals_BestReferers'|translate} {include file='Goals/templates/list_top_segment.tpl' topSegment=$topSegments.website}</li>{/if}
    <li>{'Goals_ReturningVisitorsConversionRateIs'|translate:"<b>$conversion_rate_returning%</b>"}, {'Goals_NewVisitorsConversionRateIs'|translate:"<b>$conversion_rate_new%</b>"}</li>
	</ul>
{/if}
<hr />
{$tableByConversion}

<hr />
{literal}
<style>
ul.ulGoalTopElements {
	list-style-type:circle;
	margin-left:30px;
}
.ulGoalTopElements a {
	text-decoration:none;
	color:#0033CC;
	border-bottom:1px dotted #0033CC;
	line-height:2em;
}
.goalTopElement { 
	border-bottom:1px dotted; 
} 
</style>
<script>
$(document).ready( function() {
	$('.goalTopElement')
		.tooltip()
		;
	});
</script>
{/literal}
