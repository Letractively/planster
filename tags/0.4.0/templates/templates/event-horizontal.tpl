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
				<th />
{foreach from=$groups item=group}
{if ($group->getChildren()|@count > 0) || ($group->getId() != $smarty.const.DEFAULT_GROUP)}
{	assign var=groupId value=$group->getId()}
{	if $groupId != $smarty.const.DEFAULT_GROUP}
				<th class="group" id="group{$groupId}" colspan="{$group->getChildren()|@count}">
{		include file=groupTitle.tpl}
				</th>
{	else}
				<th colspan="{$group->getChildren()|@count}" />
{	/if}
{/if}
{/foreach}
			</tr><tr>
				<th>
					<a href="legacy.php?eventID={$id}&amp;act=switch" onclick="javascript:switchOrientation('{$id}');return false;"><img src="img/switch.gif" alt="Switch orientation" title="Switch orientation" /></a>
				</th>
{foreach from=$groups item=group}
{	foreach from=$group->getChildren() item=date}
				<th onmouseover="javascript:showHandle({$date->getID()})" onmouseout="hideHandle({$date->getID()})">
					<div id="dateTitle{$date->getID()}" class="dateTitle">
{		include file="date-title.tpl"}
					</div>
				</th>
{	/foreach}
{/foreach}
			</tr>

{foreach from=$people item=person}
			<tr>
{	if $editPerson == $person->getId()}
				<th id="person-{$person->getId()}-name" style="text-align: left;">
{		include file="personNameForm.tpl"}
				</th>
{	else}
				<th style="text-align: left;" id="person-{$person->getId()}-name"><a onclick="javascript:editStatus({$person->getId()}); return false;" href="legacy.php?eventID={$id}&amp;act=editPerson&amp;person={$person->getId()}">{$person->getName()}</a></th>
{	/if}

{	foreach from=$groups item=group}
{		foreach from=$group->getChildren() item=date}
{			include file="statusCell.tpl" status=$person->getStatus($id, $date)}
{		/foreach}
{	/foreach}

{	if $editPerson == $person->getId()}
				<td class="clear">
{		include file="personFormControls.tpl"}
				</td>
{	else}
				<td class="clear" id="controls-{$person->getId()}" />
{	/if}
			</tr>
{/foreach}
{if $event->getSumType() != $smarty.const.SUM_NONE}
			<tr>
				<th class="count">Total</th>
{	foreach from=$groups item=group}
{		foreach from=$group->getChildren() item=date}
{		assign var=sum value=$date->sum()}
				<td class="count{if $sum == $maxCount}max{/if}">
					{$sum}
				</td>
{		/foreach}
{	/foreach}
			</tr>
{/if}
		</table>
