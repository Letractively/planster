<div id="main">
	<h2>PLANster setup</h2>
	<div style="font-size: small; background: none; width: 32em; height: auto">
		<b>Progress</b>
		<ul>
{foreach from=$log item=item}
{	if $item.level == LOG_MAIN}
			<li style="font-weight: bold">
{	elseif $item.level == LOG_ERROR}
			<li class="warn">
{	else}
			<li>
{	/if}
				{$item.text}
			</li>
{/foreach}
		</ul>
	</div>
</div>
