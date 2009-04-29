<div id="main">
	<h2>PLANster setup</h2>
	<div class="left" style="background: none">
		<b>Demo reset</b>
		<p>
		The following demo events have been reset:
		</p>
		<ul>
{foreach from=$demos item=demo}
			<li><a href="{$smarty.const.base_url}{$demo}">{$demo}</a></li>
{/foreach}
		</ul>
		<a href="{$smarty.const.base_url}setup/">OK</a>
	</div>
</div>
