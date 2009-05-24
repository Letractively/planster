<form method="post" action="savedate.php?event={$id}">
	<!-- for w3c's validator -->
	<div>
		<input name="date" size="10" />
		<a href="#" id="newDateTrigger" onmouseover="
			setEvent('{$id}');
{literal}
			Calendar.setup({
				align: 'BR',
				showOthers: true,
				multiple: MA,
				onClose: addMultipleDates,
				button: 'newDateTrigger',
				firstDay: 1
			});return false;"><img src="img/cal.gif" style="vertical-align: middle;" alt="calendar" title="Select date from calendar" /></a>
{/literal}
	</div>
</form>
