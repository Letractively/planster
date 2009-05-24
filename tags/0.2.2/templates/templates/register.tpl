		<div id="main">
{if $warn}
			<p class="warn">Please fill in all required values</p>
{/if}
			<form method="post" action="saveevent.php">
				<table>
					<tr>
						<td>
							Event name:
						</td><td>
							<input name="eventName" maxlength="{$smarty.const.MAX_EVENT_TITLE_LENGTH}" />
						</td>
					</tr><tr>
						<td>Expires in:</td>
						<td>
							<select name="expires">
{section name=loop start=1 loop=$maxMonths+1}
								<option>{$smarty.section.loop.index}</option>
{/section}
							</select>
							months (will be deleted from our database afterwards)
						</td>
					</tr><tr>
						<td>Your name:</td>
						<td>
							<input name="userName" maxlength="{$smarty.const.MAX_USER_NAME_LENGTH}" />
						</td>
					</tr><tr>
						<td>Your e-Mail:</td>
						<td>
							<input name="mail" maxlength="{$smarty.const.MAX_MAIL_ADDRESS_LENGTH}" /> <a href="whymail.php" onmouseover="javascript:return escape('{$whymail}');" onclick="return false;">why?</a>
						</td>
					</tr>
				</table>
				<input type="submit" value="Register" />
			</form>
		</div>
