		{assign var=status value=$person->getStatus($id, $date)}
		{assign var=statusText value=$person->getStatusText($status)}
		{if $edit == $person->getId()}
				<td class="editing">
					<input type="radio" name="status[{$date->getId()}]" onclick="javascript:updateRadioGroup({$date->getId()}, 'ok');" value="1"{if $status == STATUS_YES} checked="chedked"{/if} />
					<img src="img/ok{if $status != STATUS_YES}-not{/if}.gif" id="img{$date->getId()}-ok" alt="Yes" />
					<input type="radio" name="status[{$date->getId()}]" onclick="javascript:updateRadioGroup({$date->getId()}, 'no');" value="2"{if $status == STATUS_NO} checked="checked"{/if} />
					<img src="img/no{if $status != STATUS_NO}-not{/if}.gif" id="img{$date->getId()}-no" alt="No" />
					<input type="radio" name="status[{$date->getId()}]" onclick="javascript:updateRadioGroup({$date->getId()}, 'maybe');" value="3"{if $status == STATUS_MAYBE} checked="checked"{/if} />
					<img src="img/maybe{if $status != STATUS_MAYBE}-not{/if}.gif" id="img{$date->getId()}-maybe" alt="Maybe" />
				</td>
		{else}
				<td class="STATUS_{$status}">
			{if $status == STATUS_UNKNOWN}
					{$statusText}
			{else}
					<img src="img/{$person->getStatusIcon($status)}" alt="{$statusText}" title="{$statusText}" />
			{/if}
				</td>
		{/if}
