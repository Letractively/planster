{foreach from=$groups item=group}
{	if $group->getID() != $smarty.const.DEFAULT_GROUP}
		<input type="checkbox" name="{$group->getId()}" /> {$group->getName()}<br />
{	/if}
{/foreach}
