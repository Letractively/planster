{if $horizontal && !$first}
	<a href="movedate.php?event={$id}&amp;date={$date->getID()}&direction=-1" onclick="javascript:sndReq('moveEarlier', 'event={$id}&amp;date={$date->getID()}'); return false;"><img src="img/left.gif" style="vertical-align: middle;" alt="left" title="Move this column to the left" /></a>
{/if}
	<form name="date{$date->getID()}Form" method="post" action="savedate.php?event={$id}" onsubmit="javascript:sndReq('savedate', 'event={$id}&amp;date={$date->getID()}&amp;title=' + document.date{$date->getID()}Form.newTitle.value); return false;">
		<input type="hidden" name="date" value="{$date->getID()}" />
		<input name="newTitle" value="{$date->getDate()}" style="width: 5em;" />
	</form>
{if $horizontal && !$last}
	<a href="movedate.php?event={$id}&amp;date={$date->getID()}&direction=1" onclick="javascript:sndReq('moveLater', 'event={$id}&amp;date={$date->getID()}'); return false;"><img src="img/right.gif" style="vertical-align: middle; margin-right: 2px;" alt="right" title="Move this column to the right" /></a>
{elseif !$horizontal}
{	if !$first}
	<a href="movedate.php?event={$id}&amp;date={$date->getID()}&direction=-1" onclick="javascript:sndReq('moveEarlier', 'event={$id}&amp;date={$date->getID()}'); return false;"><img src="img/up.gif" alt="up" title="Move this row up" /></a>
{	/if}
{	if !$last}
<a href="movedate.php?event={$id}&amp;date={$date->getID()}&direction=1" onclick="javascript:sndReq('moveLater', 'event={$id}&amp;date={$date->getID()}'); return false;"><img src="img/down.gif" alt="down" title="Move this row down" /></a>
{	/if}
{/if}
