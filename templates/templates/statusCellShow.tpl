{	if $status == STATUS_UNKNOWN}
{					$statusText}
{	else}
					<img src="img/{$person->getStatusIcon($status)}" alt="{$statusText}" title="{$statusText}" />
{	/if}
