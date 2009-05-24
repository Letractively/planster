{assign var=orientation value=$event->getOrientationText()}
{assign var=id value=$event->getID()}
{assign var=people value=$event->getPeople()}
{assign var=groups value=$event->getGroups()}
{include file="navi.tpl"}
{if $editPerson}
		<form method="post" action="legacy.php?eventID={$id}&amp;act=saveStatus">
			<!-- for w3c's validator -->
			<div style="display: inline;">
				<input type="hidden" name="edit_id" value="{$editPerson}" id="edit_id" />
			</div>
{/if}
			<div id="mainTable">
{include file="event-$orientation.tpl"}
			</div>
{if $editPerson}
		</form>
{/if}
<!-- OLD -->
{if $inviteForm}
		<div id="dialog">
{	$inviteForm}
		</div>
{/if}
		<script type="text/javascript">
		<!--
			setEvent ('{$id}');
			attachDND();
		-->
		</script>
