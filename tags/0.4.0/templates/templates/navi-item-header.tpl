<div>
	<a onclick="javascript:toggleNavItem ('{$itemName}'); return false;" href="{if $legacyKey}{$id}{else}legacy.php?eventID={$event->getId()}&amp;act={$itemName}{/if}"><img id="{$itemName}Gfx" src="{$smarty.const.base_url}img/{if $legacyKey}close{else}open{/if}.gif" alt="open" class="toggle" />{$itemTitle}</a>
</div>
