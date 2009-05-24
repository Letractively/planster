{include file="closeButton.tpl" id=$id}
<h2>Invite a friend</h2>
{if $warnMail || $warnName}
	<span class="warn">Please fill-in all required fields</span>
{/if}
<form id="inviteForm" method="post" action="invite.php?event={$id}" onsubmit="javascript:invite('{$id}'); return false;">
	<p>
		Name: <input {if $warnName} class="warn"{/if}name="name"
			size="10" tabindex="1" value="{$name}" />
		<br />
		<input type="checkbox" name="sendMail"
			{if $sendMail} checked="checked"{/if}
			onchange="javascript:updateInviteFormAddressEntry()"
			tabindex="2" />Send invitation message to:
		<br />
		<input {if $warnMail} class="warn"{/if}name="mail"
			size="40" tabindex="3" value="{$mail}"
			{if !$sendMail} disabled="disabled"{/if} />
		<br />
		<span style="font-size: small">
			The e-Mail address will only be used to send the invitation.
			It is not stored in the database.
		</span>
	</p>
	<div style="text-align: right;">
		<input type="submit" value="invite" tabindex="4" />
	</div>
</form>
