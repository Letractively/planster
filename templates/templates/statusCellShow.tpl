{if $status == STATUS_UNKNOWN}
{					$statusText}
{else}
					<img src="img/{$person->getStatusIcon($status)}" alt="{$statusText}" title="{$statusText}" />
{	if $status == STATUS_MAYBE && 0}
		<a href="#" onclick="" title="{if $statusCmt}{$statusCmt}{else}Add comment{/if}"><img src="img/{if !$statusCmt}no{/if}comment.png" />
{	/if}
{/if}
