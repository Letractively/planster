<div id="main">
	<h2>PLANster setup</h2>
	<div class="left" style="background: none; width: 32em">
		<b>General status</b>
		<ul>
{foreach from=$checks key=check item=result}
			<li>
				<span class="{if $result == 'OK'}query{else}error{/if}">{$check}</span>
			</li>
{/foreach}
		</ul>
{if $db}
		<b>Database</b>
{	if $db->exists()}
		<ul>
			<li>Has been initialized</li>
{		assign var=dbVersion value=$db->detectVersion()}
			<li>Detected layout v{$dbVersion}</li>
{		if $dbVersion == $smarty.const.dblayout_version}
			<li>Layout is up to date</li>
{		else}
			<li class="error">Layout needs to be updated: <a href="?updatedb">Update now</a></li>
{		/if}
		</ul>
{	else}
		<ul>
			<li>Not initialized</li>
			<li><a href="?initdb">Initiailze now</a></li>
		</ul>
{	/if}
{/if}
	</div>
	<div class="right" style="background: none">
		<ul style="padding-left: 2em;">
			<li><a onclick="javascript: return confirm('WARNING: All data will be erased!');" href="?initdb">Initialize database</a></li>
			<li><a href="demo.php?reinit">Reset demo events</a></li>
		</ul>
	</div>
</div>
