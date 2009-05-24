{assign var=maxCount value=$groups|@planster_maxCount}
		<input type="hidden" id="available-dates" value="[
{foreach from=$groups item=group}
{	assign var=dates value=$group->getChildren()}
{	foreach from=$dates item=date}
		'{$date->getID()}',
{	/foreach}
{/foreach}
		]" />
		<input type="hidden" id="available-groups" value="[
{foreach from=$groups item=group}
{	if $group->getID() != $smarty.const.DEFAULT_GROUP}
		'{$group->getID()}',
{	/if}
{/foreach}
		]" />
		<table class="event" id="event">
			<tr>
				<th>
					<a href="legacy.php?eventID={$id}&amp;act=switch" onclick="javascript:switchOrientation('{$id}');return false;"><img src="img/switch.gif" alt="Switch orientation" title="Switch orientation" /></a>
				</th>
{foreach from=$people item=person}
{	if $editPerson == $person->getId()}
				<th id="person-{$person->getId()}-name">
{		include file="personNameForm.tpl"}
				</th>
{	else}
				<th id="person-{$person->getId()}-name"><a onclick="javascript:editStatus({$person->getId()}); return false;" href="legacy.php?eventID={$id}&amp;act=editPerson&amp;person={$person->getId()}">{$person->getName()}</a></th>
{	/if}
{/foreach}
{if $event->getSumType() != $smarty.const.SUM_NONE}
				<th class="count">Total</th>
{/if}
			</tr>

{foreach from=$groups item=group}
{	assign var=groupId value=$group->getId()}
{	if $groupId != $smarty.const.DEFAULT_GROUP}
			<tr>
{		assign var=n_people value=$people|@count}
				<th class="group" id="group{$groupId}" style="text-align: left" colspan="{if $event->getSumType() == $smarty.const.SUM_NONE}{$n_people+1}{else}{$n_people+2}{/if}">
{		include file=groupTitle.tpl}
				</th>
			</tr>
{	/if}
{	foreach from=$group->getChildren() item=date}
			<tr>
				<th onmouseover="javascript:showHandle({$date->getID()})" onmouseout="hideHandle({$date->getID()})">
					<div id="dateTitle{$date->getID()}" class="dateTitle">
{		include file="date-title.tpl"}
					</div>
				</th>
{		foreach from=$people item=person}
{			include file="statusCell.tpl" status=$person->getStatus($id, $date)}
{		/foreach}
{if $event->getSumType() != $smarty.const.SUM_NONE}
{	assign var=sum value=$date->sum()}
				<td class="count{if $sum == $maxCount}max{/if}">
					{$sum}
				</td>
{/if}
			</tr>
{	/foreach}
{/foreach}
			<tr>
				<th class="add" />
{foreach from=$people item=person}
				<td class="clear" id="controls-{$person->getId()}">{if $editPerson == $person->getId()}{include file="personFormControls.tpl"}{/if}</td>
{/foreach}
			</tr>
		</table>
