{assign var=date_id value=$date->getID()}
{assign var=date_title value=$date->getDate()}
{if $editDate != $date_id}
						<img src="img/move.gif" alt="move" class="hiddenHandle" id="handle{$date_id}" />
						<a id="date{$date_id}link" onclick="javascript:editDate({$date_id}); return false;" href="legacy.php?eventID={$id}&amp;act=editDate&amp;dateID={$date_id}">{$date_title}</a>
{/if}
{if !$editPerson}
						<form id="date{$date->getID()}Form" method="post" action="legacy.php?eventID={$id}&amp;act=saveDate" onsubmit="javascript:saveDate({$date->getID()}); return false;">
							<div>
								<input type="hidden" name="dateID" value="{$date_id}" />
								<input name="date" id="date{$date_id}input" value="{$date->getDate()}" style="width: 60px{if $editDate != $date_id}; display: none{/if}" maxlength="{$smarty.const.MAX_DATE_LENGTH}" onkeypress="javascript:closeOnEscape(event, 'dateTitle{$date->getID()}');" />
							</div>
						</form>
{/if}
