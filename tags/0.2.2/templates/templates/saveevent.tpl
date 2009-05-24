		<div id="main">
{if $error}
			<span class="error">{$error}</span>
{else}
			The event has been saved.
			<p>Please write down the following link to your event:<p>
			<p>{php}echo base_url{/php}show.php?id={$id}</p>
			<p>A message with this link has also be sent to your e-mail address, {$mail}</p>
			<p><a href="show.php?id={$id}">Proceed to the event</a></p>
{/if}
		</div>
