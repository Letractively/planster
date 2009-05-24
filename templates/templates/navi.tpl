<div id="navi">
{	include file=navi-item.tpl itemName="addPerson" itemTitle="Add person" legacyAction="savePerson" onsubmit="invite"}
{	include file=navi-item.tpl itemName="addGroup" itemTitle="Add separator" legacyAction="saveGroup" onsubmit="addGroup"}
{	include file=navi-item.tpl itemName="addItem" itemTitle="Add item" legacyAction="saveItem" onsubmit="addItem"}
{	include file=navi-item.tpl itemName="editPLAN" itemTitle="Modify this PLAN" legacyAction="savePLAN" onsubmit="saveEvent"}
{	include file=navi-item.tpl itemName="clonePLAN" itemTitle="Duplicate this PLAN" legacyAction="clonePLANsave" onsubmit="clone"}
{	include file="adsense.tpl"}
</div>
