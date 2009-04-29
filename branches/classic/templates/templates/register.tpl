		<div id="main">
			<h2>What are you PLANning?
{if $warn}
			<p class="warn">Please fill in all required values</p>
{/if}
			</h2>
			<form method="post" action="saveevent.php" onsubmit="javascript:return validateRegistrationForm();">
				<div class="left">
					<b>The PLAN</b>
					<p>What are you up to?</p>
					<input name="eventName" maxlength="{$smarty.const.MAX_EVENT_TITLE_LENGTH}" id="eventName" tabindex="1" /><br />
					<span id="eventNameWarning"></span>
				</div>
				<div class="center">
					<b>Time</b>
					<p>How long do you need for PLANning?</p>
					<select name="expires" tabindex="2" style="width: 3em;">
	{section name=loop start=1 loop=$maxMonths+1}
						<option>{$smarty.section.loop.index}</option>
	{/section}			</select>
						month(s)
					<p>Your <span class="PLAN">PLAN</span> will be removed from our database afterwards, so choose wisely...
					</p>
				</div>
				<div class="right">
					<b>You</b>
					<p>
						Your name:<br />
						<input name="userName" maxlength="{$smarty.const.MAX_USER_NAME_LENGTH}" tabindex="3" id="userName" /><br />
						<span id="userNameWarning"></span>
						<br /><br />
						Your e-Mail address:
						<input name="mail" maxlength="{$smarty.const.MAX_MAIL_ADDRESS_LENGTH}" tabindex="4" />
					</p>
					<p>
						<a href="{$smarty.const.base_url}" tabindex="5"><img src="{$smarty.const.base_url}img/cancel.gif" alt="cancel" /></a>
						<input type="image" src="{$smarty.const.base_url}img/accept.gif" tabindex="6" />
					</p>
				</div>
			</form>
		</div>
		<script type="text/javascript">
		<!--
			var element = document.getElementById('eventName');
			element.focus();
		-->
		</script>
