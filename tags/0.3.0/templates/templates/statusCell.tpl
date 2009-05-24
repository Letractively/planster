{assign var=statusText value=$person->getStatusText($status)}
{if $editPerson == $person->getId()}
	<td id="p{$person->getId()}d{$date->getId()}" class="editing">
{	include file="statusCellEdit.tpl"}
{else}
	<td class="STATUS_{$status}" id="p{$person->getId()}d{$date->getId()}">
{	include file="statusCellShow.tpl"}
{/if}
	</td>
