<ul class="nav">
{foreach from=$menu key=level1 item=level2 name=menu}
<li>
	<a name='{$level2._url|@urlRewriteWithParameters}' href='index.php{$level2._url|@urlRewriteBasicView}'>{$level1|translate}</a>
	<ul>
	{foreach from=$level2 key=name item=urlParameters name=level2}
		{if $name != '_url'}
		<li><a name='{$urlParameters|@urlRewriteWithParameters}' href='index.php{$urlParameters|@urlRewriteBasicView}'>{$name|translate}</a></li>
		{/if}
 	{/foreach}
 	</ul>
</li>
{/foreach}
</ul>
