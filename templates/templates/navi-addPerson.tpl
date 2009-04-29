					<p id="addPersonError" style="display: none" class="error">Please fill in all the fields</p>
					Name:<br />
					<input class="text" name="name" id="addPersonMainInput" maxlength="{$smarty.const.MAX_USER_NAME_LENGTH}" /><br />
					<input name="sendMail" id="addPersonInvite" type="checkbox" onclick="javascript:updateInviteFormAddressEntry();" />Send invitation to:<br />
					<input class="text" name="mail" id="addPersonMailInput" maxlength="{$smarty.const.MAX_MAIL_ADDRESS_LENGTH}" />
					<script type="text/javascript"><!--
						updateInviteFormAddressEntry();
					--></script>
{				include file=navi-submit-button.tpl text="Add"}
