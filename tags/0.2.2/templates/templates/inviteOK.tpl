{include file="closeButton.tpl" id=$id}
			<div style="text-align: center">
{if $mail}
			An invitation has been sent to<br />{$mail}.
{else}
			The new user "{$name}" was successfully added to the event
{/if}
			</div>
