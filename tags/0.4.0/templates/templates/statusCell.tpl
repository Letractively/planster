{assign var=statusText value=$person->getStatusText($status)}
{if $status == STATUS_MAYBE && 0}
{	assign var=statusCmt value=$person->getStatusComment($date->getId())}
{/if}
{if $editPerson == $person->getId()}
	<td id="p{$person->getId()}d{$date->getId()}" class="editing">
{	include file="statusCellEdit.tpl"}
{else}
	<td class="STATUS_{$status}" id="p{$person->getId()}d{$date->getId()}">
{	include file="statusCellShow.tpl"}
{/if}
	</td>
