{desire_valueOf var="legacy_$itemName" assign="legacyKey"}
<ul>
	<li id="{$itemName}">
		<form id="{$itemName}Form" method="post" action="legacy.php?eventID={$event->getId()}&amp;act={$legacyAction}" onsubmit="javascript:{$onsubmit}();return false" onkeypress="javascript:closeNaviOnEscape(event); return true">
{			include file=navi-corners.tpl}
{			include file=navi-item-header.tpl}
			<div id="{$itemName}Body" {if !$legacyKey}style="display: none" {/if}class="body">
{				include file=navi-$itemName.tpl}
			</div>
		</form>
	</li>
</ul>
