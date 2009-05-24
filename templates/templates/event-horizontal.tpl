{if $edit}
		<form method="post" action="savestatus.php">
			<input type="hidden" name="id" value="{$id}" />
			<input type="hidden" name="edit_id" value="{$edit}" />
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
					{$editDateForm}
{	else}
{		include file="date-title.tpl" id=$id date_id=$date->getID() date_title=$date->getDate()}
{	/if}
					</div>
				</th>
{/foreach}
				<th class="add">
					<div id="newdate">
{if $addDate}
						<form method="post" action="savedate.php?event={$id}"><input name="date" size="10" /></form>
{else}
						<a onclick="javascript:getDateForm('{$id}'); return false;" href="?id={$id}&amp;adddate"><img src="img/add.gif" alt="Add Date" title="Add Date" /></a>
{/if}
					</div>
				</th>
			</tr>

{foreach from=$people item=person}
			<tr>
	{if $edit == $person->getId()}
				<th><input name="personName" value="{$person->getName()}" size="10" /></th>
	{else}
				<th><a onclick="javascript:editStatus('{$id}',{$person->getId()}); return false;" href="?id={$id}&amp;edit={$person->getId()}">{$person->getName()}</a></th>
	{/if}

	{foreach from=$dates item=date}
		{assign var=status value=$person->getStatus($id, $date)}
		{assign var=statusText value=$person->getStatusText($status)}
		{if $edit == $person->getId()}
				<td class="editing">
					<input type="radio" name="status[{$date->getId()}]" onclick="javascript:updateRadioGroup({$date->getId()}, 'ok');" value="1"{if $status == STATUS_YES} checked="chedked"{/if} />
					<img src="img/ok{if $status != STATUS_YES}-not{/if}.gif" name="img{$date->getId()}-ok" alt="Yes" />
					<input type="radio" name="status[{$date->getId()}]" onclick="javascript:updateRadioGroup({$date->getId()}, 'no');" value="2"{if $status == STATUS_NO} checked="checked"{/if} />
					<img src="img/no{if $status != STATUS_NO}-not{/if}.gif" name="img{$date->getId()}-no" alt="No" />
					<input type="radio" name="status[{$date->getId()}]" onclick="javascript:updateRadioGroup({$date->getId()}, 'maybe');" value="3"{if $status == STATUS_MAYBE} checked="checked"{/if} />
					<img src="img/maybe{if $status != STATUS_MAYBE}-not{/if}.gif" name="img{$date->getId()}-maybe" alt="Maybe" />
				</td>
		{else}
				<td class="STATUS_{$status}">
			{if $status == STATUS_UNKNOWN}
					{$statusText}
			{else}
					<img src="img/{$person->getStatusIcon($status)}" alt="{$statusText}" title="{$statusText}" />
			{/if}
				</td>
		{/if}
	{/foreach}
	{if $edit}
		{if $edit == $person->getId()}
				<td class="clear"><input type="submit" value="save" /></td>
		{else}
				<td class="clear" />
		{/if}
	{/if}
			</tr>
{/foreach}
		</table>
{if $edit}
	</form>
{/if}
	<div id="invite">
{if $inviteForm}
{	$inviteForm}
{else}
{	include file="inviteBox.tpl" id=$id}
{/if}
	</div>
