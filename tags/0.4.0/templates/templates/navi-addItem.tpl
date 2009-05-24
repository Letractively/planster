					<p id="addItemError" style="display: none" class="error">Please enter a valid name for your item</p>
					<input class="text" name="name" id="addItemMainInput" maxlength="{$smarty.const.MAX_DATE_LENGTH}" />
					<script type="text/javascript"><!--
						document.write ('<a href="#" onclick="javascript:toggleCal (\'addItem\'); return false"><img src="{$smarty.const.base_url}img/cal.gif" alt="calendar" class="calIcon" title="Toggle calendar" /><\/a>');
					--></script>
					<br />
					<p id="addItemCal" style="display: none"></p>
					<script type="text/javascript"><!--
{literal}
						Calendar.setup ({
							flat: 'addItemCal',
							firstDay : 1,
							flatCallback : addItemDateChanged});
{/literal}
					--></script>
					<p id="assignParagraph"{if $groups|@count < 2} style="display: none"{/if}>
					Assign to
					</p>
					<div id="groupBoxes">
{include file="navi-groupBoxes.tpl"}
					</div>
{				include file=navi-submit-button.tpl text="Add"}
