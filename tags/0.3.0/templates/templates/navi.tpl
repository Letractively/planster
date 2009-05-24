<div id="navi"> 
	<ul>
		<li id="addPerson">
			<form id="addPersonForm" method="post" action="legacy.php?eventID={$event->getId()}&amp;act=savePerson" onsubmit="javascript:invite();return false" onkeypress="javascript:closeNaviOnEscape(event); return true">
				<div class="corner tl"></div>
				<div class="corner tr"></div>
				<div class="corner bl"></div>
				<div class="corner br"></div>
				<div>
					<a onclick="javascript:toggleNavItem ('addPerson'); return false;" href="{if $legacy_addPerson}{$id}{else}legacy.php?eventID={$event->getId()}&amp;act=addPerson{/if}"><img id="addPersonGfx" src="{$smarty.const.base_url}img/{if $legacy_addPerson}close{else}open{/if}.gif" alt="open" class="toggle" />Add person</a>
				</div>
				<div id="addPersonBody" {if !$legacy_addPerson}style="display: none" {/if}class="body">
					<p id="addPersonError" style="display: none" class="error">Please fill in all the fields</p>
					Name:<br />
					<input class="text" name="name" id="addPersonMainInput" maxlength="{$smarty.const.MAX_USER_NAME_LENGTH}" /><br />
					<input name="sendMail" id="addPersonInvite" type="checkbox" onclick="javascript:updateInviteFormAddressEntry();" />Send invitation to:<br />
					<input class="text" name="mail" id="addPersonMailInput" maxlength="{$smarty.const.MAX_MAIL_ADDRESS_LENGTH}" /><br />
					<script type="text/javascript"><!--
						updateInviteFormAddressEntry();
					--></script>
					<input type="submit" class="submit" value="save" /><br />
				</div>
			</form>
		</li>
	</ul>
	<ul>
		<li id="addGroup">
			<form id="addGroupForm" method="post" action="legacy.php?eventID={$event->getId()}&amp;act=saveGroup" onsubmit="javascript:addGroup();return false" onkeypress="javascript:closeNaviOnEscape(event); return true">
				<div class="corner tl"></div>
				<div class="corner tr"></div>
				<div class="corner bl"></div>
				<div class="corner br"></div>
				<div>
					<a onclick="javascript:toggleNavItem ('addGroup'); return false;" href="{if $legacy_addGroup}{$id}{else}legacy.php?eventID={$event->getId()}&amp;act=addGroup{/if}"><img id="addGroupGfx" src="{$smarty.const.base_url}img/{if $legacy_addGroup}close{else}open{/if}.gif" alt="open" class="toggle" />Add separator</a>
				</div>
				<div id="addGroupBody" {if !$legacy_addGroup}style="display: none" {/if}class="body">
					<p id="addGroupError" style="display: none" class="error">Please fill in all the fields</p>
					<input class="text" name="name" id="addGroupMainInput" maxlength="{$smarty.const.MAX_GROUP_NAME_LENGTH}" />
					<script type="text/javascript"><!--
						document.write ('<a href="#" onclick="javascript:toggleCal (\'addGroup\'); return false"><img src="{$smarty.const.base_url}img/cal.gif" alt="calendar" class="calIcon" title="Toggle calendar" /><\/a>');
					--></script>
					<br />
					<p id="addGroupCal" style="display: none"></p>
					<script type="text/javascript"><!--
{literal}
						var cal = Calendar.setup ({
							flat : 'addGroupCal',
							firstDay : 1,
							flatCallback : addGroupDateChanged});
{/literal}
					--></script>
					<script type="text/javascript"><!--
						updateInviteFormAddressEntry();
					--></script>
					<input type="submit" class="submit" value="save" /><br />
				</div>
			</form>
		</li>
	</ul>
	<ul>
		<li id="addItem">
			<form id="addItemForm" method="post" action="legacy.php?eventID={$event->getId()}&amp;act=saveItem" onsubmit="javascript:addItem();return false" onkeypress="javascript:closeNaviOnEscape(event); return true">
				<div class="corner tl"></div>
				<div class="corner tr"></div>
				<div class="corner bl"></div>
				<div class="corner br"></div>
				<div>
					<a onclick="javascript:toggleNavItem ('addItem'); return false;" href="{if $legacy_addItem}{$id}{else}legacy.php?eventID={$event->getId()}&amp;act=addItem{/if}"><img id="addItemGfx" src="{$smarty.const.base_url}img/{if $legacy_addItem}close{else}open{/if}.gif" alt="open" class="toggle" />Add item</a>
				</div>
				<div id="addItemBody" {if !$legacy_addItem}style="display: none" {/if}class="body">
					<p id="addItemError" style="display: none" class="error">Please enter a valid name for your item</p>
					<input class="text" name="name" id="addItemMainInput" maxlength="{$smarty.const.MAX_DATE_LENGTH}" />
					<script type="text/javascript"><!--
						document.write ('<a href="#" onclick="javascript:toggleCal (\'addItem\'); return false"><img src="{$smarty.const.base_url}img/cal.gif" alt="calendar" class="calIcon" title="Toggle calendar" /><\/a>');
					--></script>
					<br />
					<p id="addItemCal" style="display: none"></p>
					<script type="text/javascript"><!--
{literal}
						Calendar.setup ({
							flat: 'addItemCal',
							firstDay : 1,
							flatCallback : addItemDateChanged});
{/literal}
					--></script>
					<p>
					Assign to
					</p>
					<div id="groupBoxes">
{include file="navi-groupBoxes.tpl"}
					</div>
					<input type="submit" class="submit" value="save" /><br />
				</div>
			</form>
		</li>
	</ul>
	<ul>
		<li id="editPLAN">
			<form id="editPLANForm" method="post" action="legacy.php?eventID={$event->getId()}&amp;act=savePLAN" onsubmit="javascript:saveTitle();return false" onkeypress="javascript:closeNaviOnEscape(event); return true">
				<div class="corner tl"></div>
				<div class="corner tr"></div>
				<div class="corner bl"></div>
				<div class="corner br"></div>
				<div>
					<a onclick="javascript:toggleNavItem ('editPLAN'); return false;" href="{if $legacy_editPLAN}{$id}{else}legacy.php?eventID={$event->getId()}&amp;act=editPLAN{/if}"><img id="editPLANGfx" src="{$smarty.const.base_url}img/{if $legacy_editPLAN}close{else}open{/if}.gif" alt="open" class="toggle" />Rename this PLAN</a>
				</div>
				<div id="editPLANBody" {if !$legacy_editPLAN}style="display: none" {/if}class="body">
					<p id="editPLANError" style="display: none" class="error">Please fill in all the fields</p>
					<input class="text" name="name" id="editPLANMainInput" value="{$event->getName()}" maxlength="{$smarty.const.MAX_EVENT_TITLE_LENGTH}" /><br />
					<input type="submit" class="submit" value="save" /><br />
				</div>
			</form>
		</li>
	</ul>
	<ul>
		<li id="clonePLAN">
			<form id="clonePLANForm" method="post" action="legacy.php?eventID={$event->getId()}&amp;act=clonePLANsave" onsubmit="javascript:clone();return false" onkeypress="javascript:closeNaviOnEscape(event); return true">
				<div class="corner tl"></div>
				<div class="corner tr"></div>
				<div class="corner bl"></div>
				<div class="corner br"></div>
				<div>
					<a onclick="javascript:toggleNavItem ('clonePLAN'); return false;" href="{if $legacy_clonePLAN}{$id}{else}legacy.php?eventID={$event->getId()}&amp;act=clonePLAN{/if}"><img id="clonePLANGfx" src="{$smarty.const.base_url}img/{if $legacy_clonePLAN}close{else}open{/if}.gif" alt="open" class="toggle" />Clone this PLAN</a>
				</div>
				<div id="clonePLANBody" {if !$legacy_clonePLAN}style="display: none" {/if}class="body">
					<p id="clonePLANError" style="display: none" class="error">Please fill in all the fields</p>
					Valid for
					<select name="expires" id="clonePLANMainInput">
{section name=loop start=1 loop=$smarty.const.max_age_for_event+1}
						<option{if $smarty.section.loop.index == 6} selected="selected"{/if}>{$smarty.section.loop.index}</option>
{/section}
					</select>
					months
					<p>
						<input type="checkbox" name="clonePeople" onchange="javascript:updateCloneStatusBox()" />Clone people<br />
						<input type="checkbox" name="cloneDates" onchange="javascript:updateCloneStatusBox()" />Clone items<br />
						<input type="checkbox" name="cloneStatus" />Clone status<br />
						<script type="text/javascript">
						<!--
							var form = document.getElementById ('clonePLANForm');
							form.cloneStatus.disabled = true;
						-->
						</script>
					</p>
					<input type="submit" class="submit" value="clone" /><br />
				</div>
			</form>
		</li>
	</ul>
</div>
