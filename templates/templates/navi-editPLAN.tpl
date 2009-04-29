					<p id="editPLANError" style="display: none" class="error">Please fill in all the fields</p>
					<p>
					<input class="text" name="name" id="editPLANMainInput" value="{$event->getName()}" maxlength="{$smarty.const.MAX_EVENT_TITLE_LENGTH}" /><br />
{assign var=sumtype value=$event->getSumType()}
					<input name="sum" type="radio" value="{$smarty.const.SUM_NONE}"{if $sumtype == $smarty.const.SUM_NONE} checked="checked"{/if} />Hide totals<br />
					<input name="sum" type="radio" value="{$smarty.const.SUM_YES}"{if $sumtype == $smarty.const.SUM_YES} checked="checked"{/if} />Count only <img src="{$smarty.const.base_url}img/ok.gif" alt="Yes" title="" /><br />
					<input name="sum" type="radio" value="{$smarty.const.SUM_BOTH}"{if $sumtype == $smarty.const.SUM_BOTH} checked="checked"{/if} />Count  <img src="{$smarty.const.base_url}img/maybe.gif" alt="Maybe" title="" /> as half <img src="{$smarty.const.base_url}img/ok.gif" alt="Yes" title="" /><br />
					</p>
{				include file=navi-submit-button.tpl text="Save"}
