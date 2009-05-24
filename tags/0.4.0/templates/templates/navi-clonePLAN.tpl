					<p id="clonePLANError" style="display: none" class="error">Please fill in all the fields</p>
					Valid for
					<select name="expires" id="clonePLANMainInput">
{section name=loop start=1 loop=$smarty.const.max_age_for_event+1}
						<option{if $smarty.section.loop.index == 6} selected="selected"{/if}>{$smarty.section.loop.index}</option>
{/section}
					</select>
					months
					<p>
						<input type="checkbox" name="clonePeople" onchange="javascript:updateCloneStatusBox()" />Copy people<br />
						<input type="checkbox" name="cloneDates" onchange="javascript:updateCloneStatusBox()" />Copy items<br />
						<input type="checkbox" name="cloneStatus" />Copy status<br />
						<script type="text/javascript">
						<!--
							var form = document.getElementById ('clonePLANForm');
							form.cloneStatus.disabled = true;
						-->
						</script>
					</p>
{				include file=navi-submit-button.tpl text="Save"}
