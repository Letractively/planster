{if $horizontal && !$first}
	<a href="movedate.php?event={$id}&amp;date={$date->getID()}&amp;direction=-1" onclick="javascript:sndReq('moveEarlier', 'event={$id}&amp;date={$date->getID()}'); return false;"><img src="img/left.gif" style="vertical-align: middle;" alt="left" title="Move this column to the left" /></a>
{/if}
	<form id="date{$date->getID()}Form" method="post" action="savedate.php?event={$id}" onsubmit="javascript:saveDate('{$id}', {$date->getID()}); return false;">
		<!-- divs are to make w3c's validator happy -->
		<div style="display: inline;">
			<input type="hidden" name="date" value="{$date->getID()}" />
			<input name="newTitle" id="newTitle" value="{$date->getDate()}" style="width: 60px;" />
{literal}
			<a href="#" id="trigger" onmouseover="Calendar.setup({
				inputField	: 'newTitle',
				ifFormat	: '%a, %b %d',
				button		: 'trigger',
				firstDay	: 1
				});return false;"><img src="img/cal.gif" style="vertical-align: middle;" alt="calendar" title="Select date from calendar" /></a>
{/literal}
		</div>
	</form>
{if $horizontal && !$last}
	<a href="movedate.php?event={$id}&amp;date={$date->getID()}&amp;direction=1" onclick="javascript:sndReq('moveLater', 'event={$id}&amp;date={$date->getID()}'); return false;"><img src="img/right.gif" style="vertical-align: middle;" alt="right" title="Move this column to the right" /></a>
{elseif !$horizontal}
{	if !$first}
	<a href="movedate.php?event={$id}&amp;date={$date->getID()}&amp;direction=-1" onclick="javascript:sndReq('moveEarlier', 'event={$id}&amp;date={$date->getID()}'); return false;"><img src="img/up.gif" alt="up" title="Move this row up" /></a>
{	/if}
{	if !$last}
<a href="movedate.php?event={$id}&amp;date={$date->getID()}&amp;direction=1" onclick="javascript:sndReq('moveLater', 'event={$id}&amp;date={$date->getID()}'); return false;"><img src="img/down.gif" alt="down" title="Move this row down" /></a>
{	/if}
{/if}
