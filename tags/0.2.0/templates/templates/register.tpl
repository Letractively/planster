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
							<input name="eventName" />
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
							<input name="userName" />
						</td>
					</tr><tr>
						<td>Your e-Mail:</td>
						<td>
							<input name="mail" /> <a href="whymail.php" onmouseover="javascript:return escape('{$whymail}');" onclick="return false;">why?</a>
						</td>
					</tr>
				</table>
				<input type="submit" value="Register" />
			</form>
		</div>
