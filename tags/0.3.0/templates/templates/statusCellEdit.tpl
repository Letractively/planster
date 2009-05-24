					<input type="radio" id="status{$date->getId()}-ok" name="status[{$date->getId()}]" onclick="javascript:updateRadioGroup({$date->getId()}, 'ok');" value="1"{if $status == STATUS_YES} checked="chedked"{/if} />
					<img src="img/ok{if $status != STATUS_YES}-not{/if}.gif" id="img{$date->getId()}-ok" alt="Yes" />
{if $event->getOrientation() == $smarty.const.ORIENTATION_HORIZONTAL}
					<br />
{/if}
					<input type="radio" id="status{$date->getId()}-no" name="status[{$date->getId()}]" onclick="javascript:updateRadioGroup({$date->getId()}, 'no');" value="2"{if $status == STATUS_NO} checked="checked"{/if} />
					<img src="img/no{if $status != STATUS_NO}-not{/if}.gif" id="img{$date->getId()}-no" alt="No" />
{if $event->getOrientation() == $smarty.const.ORIENTATION_HORIZONTAL}
					<br />
{/if}
					<input type="radio" id="status{$date->getId()}-maybe" name="status[{$date->getId()}]" onclick="javascript:updateRadioGroup({$date->getId()}, 'maybe');" value="3"{if $status == STATUS_MAYBE} checked="checked"{/if} />
					<img src="img/maybe{if $status != STATUS_MAYBE}-not{/if}.gif" id="img{$date->getId()}-maybe" alt="Maybe" />
{if $event->getOrientation() == $smarty.const.ORIENTATION_HORIZONTAL}
					<br />
{/if}
