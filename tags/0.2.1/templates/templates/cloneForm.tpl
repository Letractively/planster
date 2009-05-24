{include file="closeButton.tpl" id=$id}
<h2>Clone this event</h2>
{if $warnOwner}<p class="warn">Please enter a valid e-Mail address for the new owner</p>{/if}
{if $warnStatus}<p class="warn">In order to clone statuses you will also need to clone people and dates</p>{/if}
<form id="cloneForm" method="post" action="clone.php?event={$id}"
	onsubmit="javascript:clone('{$id}'); return false;">
	<table>
		<tr>
			<td class="clone">New event's name:</td>
			<td class="clone"><input {if $warnName} class="warn"{/if}name="name" size="20" tabindex="1" value="{$name}" /></td>
		</tr><tr>
			<td class="clone">Expires in:</td>
			<td>
				<select name="expires">
{section name=loop start=1 loop=$maxMonths+1}
					<option{if $expires == $smarty.section.loop.index} selected="selected"{/if}>{$smarty.section.loop.index}</option>
{/section}
				</select> Months
			</td>
		</tr><tr>
			<td class="clone">Owner's e-Mail:</td>
			<td class="clone">
				<input type="checkbox" name="cloneOwner" {if $cloneOwner} checked="checked"{/if}{if !$nojs} onchange="javascript:updateOwnerMailEntry()"{/if} />Clone
				<input name="owner" value="{$owner}"{if $cloneOwner && !$nojs} disabled="disabled"{/if} />
			</td>
		</tr><tr>
			<td class="clone">Items to clone:</td>
			<td class="clone">
				<input type="checkbox" name="cloneDates"{if $cloneDates} checked="checked"{/if}{if !$nojs} onchange="javascript:updateCloneStatusBox();"{/if} />Dates / items<br />
				<input type="checkbox" name="clonePeople"{if $clonePeople} checked="checked"{/if}{if !$nojs} onchange="javascript:updateCloneStatusBox();"{/if} />People<br />
				<input type="checkbox" name="cloneStatus"{if $cloneStatus} checked="checked"{/if}{if $disableCloneStatus} disabled="disabled"{/if} />Statuses<br />
			</td>
		</tr>
	</table>
			
	<div style="text-align: right;">
		<input type="submit" value="clone" tabindex="4" />
	</div>
</form>
