{if $groupId != $smarty.const.DEFAULT_GROUP}
{	if $legacy_editGroup != $groupId}
<a href="{$smarty.const.base_url}legacy.php?eventID={$id}&amp;act=editGroup&amp;groupID={$groupId}" id="groupName{$groupId}" onclick="editGroupName({$groupId}); return false">{$group->getName()}</a>
{	/if}
{	if !$editPerson}
<form method="post" action="{$smarty.const.base_url}legacy.php?eventID={$id}&amp;act=saveGroup&amp;groupID={$groupId}" onsubmit="javascript:saveGroupName({$groupId}); return false;">
	<div><input {if $legacy_editGroup != $groupId}style="display: none" {/if}name="name" id="groupName{$groupId}-edit" value="{$group->getName()}" onkeypress="javascript:closeOnEscape(event, 'group{$groupId}');" size="10" /></div>
</form>
{	/if}
{/if}
