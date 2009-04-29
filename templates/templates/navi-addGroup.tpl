					<p id="addGroupError" style="display: none" class="error">Please fill in all the fields</p>
					<input class="text" name="name" id="addGroupMainInput" maxlength="{$smarty.const.MAX_GROUP_NAME_LENGTH}" />
					<script type="text/javascript"><!--
						document.write ('<a href="#" onclick="javascript:toggleCal (\'addGroup\'); return false"><img src="{$smarty.const.base_url}img/cal.gif" alt="calendar" class="calIcon" title="Toggle calendar" /><\/a>');
					--></script>
					<br />
					<p id="addGroupCal" style="display: none"></p>
					<script type="text/javascript"><!--
{literal}
						var cal = Calendar.setup ({
							flat : 'addGroupCal',
							firstDay : 1,
							flatCallback : addGroupDateChanged});
{/literal}
					--></script>
					<script type="text/javascript"><!--
						updateInviteFormAddressEntry();
					--></script>
{				include file=navi-submit-button.tpl text="Add"}
