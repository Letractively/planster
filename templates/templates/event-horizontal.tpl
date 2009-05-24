{if $edit}
		<form method="post" action="savestatus.php">
			<!-- for w3c's validator -->
			<div style="display: inline;">
				<input type="hidden" name="id" value="{$id}" />
				<input type="hidden" name="edit_id" value="{$edit}" />
			</div>
{/if}
		<table class="event">
			<tr>
				<th>
					<a href="?id={$id}&amp;switch" onclick="javascript:switchOrientation('{$id}');return false;"><img src="img/switch.gif" alt="Switch orientation" title="Switch orientation" /></a>
				</th>
{foreach from=$dates item=date}
				<th>
					<div id="dateTitle{$date->getID()}">
{	if $editdate == $date->getID()}
{		$editDateForm}
{	else}
{		include file="date-title.tpl" id=$id date_id=$date->getID() date_title=$date->getDate()}
{	/if}
					</div>
				</th>
{/foreach}
				<th class="add">
					<div id="newdate">
{if $addDate}
{	include file="adddate.tpl" id=$id}
{else}
						<a onclick="javascript:getDateForm('{$id}'); return false;" href="?id={$id}&amp;adddate"><img src="img/add.gif" alt="Add Date" title="Add Date" /></a>
{/if}
					</div>
				</th>
			</tr>

{foreach from=$people item=person}
			<tr>
{	if $edit == $person->getId()}
				<th><input name="personName" id="personName" value="{$person->getName()}" size="10" maxlength="{$smarty.const.MAX_USER_NAME_LENGTH}" /></th>
{	else}
				<th><a onclick="javascript:editStatus('{$id}',{$person->getId()}); return false;" href="?id={$id}&amp;edit={$person->getId()}">{$person->getName()}</a></th>
{	/if}

{	foreach from=$dates item=date}
{		include file="statusCell.tpl"}
{	/foreach}

{	if $edit}
{		if $edit == $person->getId()}
				<td class="clear"><input type="submit" value="save" /></td>
{		else}
				<td class="clear" />
{		/if}
{	/if}
			</tr>
{/foreach}
		</table>
{if $edit}
	</form>
{/if}
{	include file="eventActions.tpl" id=$id}
	<div id="dialog">
{if $inviteForm}
{	$inviteForm}
{/if}
	</div>
