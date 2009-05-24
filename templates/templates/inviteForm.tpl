<div style="padding-top: 10px">
	<form name="inviteForm" method="post" action="invite.php?event={$id}" onsubmit="javascript:invite('{$id}'); return false;">
		<span{if $warnName} class="warn"{/if}>Name:</span> <input name="name" size="10" tabindex="1" value="{$name}" />
		<span{if $warnMail} class="warn"{/if}>e-Mail:</span> <input name="mail" size="15" tabindex="2" value="{$mail}" />
		<input type="submit" value="invite" tabindex="3" />
	</form>
</div>
