		<div id="main">
			<h2>Ready</h2>
			<div class="left" style="background: none">
{if $error}
			<span class="error">{$error}</span>
{else}
			<p>
				Your <span class="PLAN">PLAN</span> has been saved and is now ready.
			</p>
			<p>
{	if $mail}
				An e-Mail message with the corresponding link
				has been sent to {$mail}. You can also access
				it directly from here by clicking below:
{	else}
				To access it, click the following link:
{	/if}
			</p>
			<p><a href="{$id}">{$smarty.const.base_url}{$id}</a></p>
{/if}
			</div>
		</div>
