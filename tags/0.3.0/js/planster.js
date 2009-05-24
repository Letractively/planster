/*
 * PLANster
 * Copyright (C) 2004-2007 Stefan Ott. All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * $Id: planster.js 410 2007-03-15 03:14:48Z stefan $
 */

// The person which is being edited
var editPerson = { id : -1, cells : new Array () }

// The current event'd ID
var EVENT_ID;

// The date-title forms which are currently being displayed
var openDateTitles = new Array ();

// The currently open menu item
var openMenuItem = '';

function refresh_status ()
{
	detachDND ();
	http_get (EVENT_ID + '/status');
}

function refresh_group_boxes ()
{
	http_get (EVENT_ID + '/groupboxes');
}

function apply_data (result)
{
	switch (result.target)
	{
		case 'groupBoxes':
			var target = document.getElementById ('groupBoxes');
			target.innerHTML = result.data;
			break;
		case 'eventTable':
			var target = document.getElementById ('mainTable');
			target.innerHTML = result.data;
			attachDND ();
			break;
		case 'user':
			var cell = document.getElementById ('person-' + result.id + '-name');
			cell.innerHTML = result.nameForm;

			var dates = result.data;
			for (i=0; i < dates.length; i++)
			{
				var date = dates [i];
				var cellID = 'p' + result.id + 'd' + date.id;

				cell = document.getElementById(cellID);

				editPerson.cells[editPerson.cells.length] = {
					'id'		: cellID,
					'html'		: cell.innerHTML,
					'cssClass'	: cell.className
				}

				cell.className = 'editing';
				cell.innerHTML = date.html;
			}

			cell = document.getElementById ('controls-' + result.id);
			cell.innerHTML = result.controls;

			apply_focus ();
			break;
		case 'title':
			var h1 = document.getElementsByTagName ('h1') [0];
			h1.innerHTML = result.data;

			apply_focus ();
			showMessage ('The PLAN was renamed');
			break;
		default:
			if (DEBUG) alert ('Unknown target: ' + result.target);
	}
}

function process_message (result)
{
	switch (result)
	{
		case 'INVITE_OK':
			clearInviteForm ();
			refresh_status ();
			showMessage ('The new person was added');
			break;
		case 'INVITE_FAILED':
			showElement ('addPersonError');
			break;
		case 'ADD_GROUP_OK':
			clearAddGroupForm ();
			refresh_group_boxes ();
			refresh_status ();
			showMessage ('The new separator was added');
			break;
		case 'ADD_GROUP_FAILED':
			showElement ('addGroupError');
			break;
		case 'ADD_ITEM_OK':
			clearAddItemForm ();
			refresh_status ();
			showMessage ('The new item was added');
			break;
		case 'ADD_ITEM_FAIL':
			showElement ('addItemError');
			break;
		case 'SWITCH_OK':
		case 'MOD_GROUP_OK':
		case 'MOVE_ITEM_OK':
		case 'RENAME_ITEM_OK':
		case 'ADD_TO_GROUP_OK':
		case 'UPDATE_STATUS_OK':
			refresh_status ();
			break;
		default:
			showMessage (result);
	}
}

function showMessage (message)
{
	var element = document.getElementById ('messageText');
	element.innerHTML = message;
	setTimeout ("fadeMessage()", 3000);
	Effect.Appear ('message', { 'duration' : '0.1' });
}

function fadeMessage ()
{
	var element = document.getElementById ('message');
	Effect.Fade ('message', { 'duration' : '0.4' });
}

function setEvent (eventID)
{
	EVENT_ID = eventID;
}

function editGroupName (groupID)
{
	save ('group' + groupID);
	hideElement ('groupName' + groupID);
	scheduledFocus = 'groupName' + groupID + '-edit';
	showElement ('groupName' + groupID + '-edit');
	apply_focus ();
}

function saveGroupName (groupID)
{
	var name = getValue ('groupName' + groupID + '-edit');

	if ((name == "") && !confirm ('Do you really wish to delete this group?'))
	{
		return;
	}

	var data = {
		'name'	: encodeURIComponent (name)
	}
	http_post ('group/' + groupID, data.toJSONString ());
}

function clearInviteForm ()
{
	form = document.getElementById ('addPersonForm');
	form.name.value = '';
	form.mail.value = '';
	form.sendMail.checked = false;
	hideElement ('addPersonError');
	updateInviteFormAddressEntry ();
	scheduledFocus = 'addPersonMainInput';
	apply_focus ();
}

function clearAddGroupForm ()
{
	form = document.getElementById ('addGroupForm');
	form.name.value = '';
	hideElement ('addGroupError');
	scheduledFocus = 'addGroupMainInput';
	apply_focus ();
}

function clearAddItemForm ()
{
	form = document.getElementById ('addItemForm');
	form.name.value = '';
	hideElement ('addItemError');
	scheduledFocus = 'addItemMainInput';
	apply_focus ();
}

function invite ()
{
	form = document.getElementById ('addPersonForm');

	var data = {
		'name'		: encodeURIComponent (form.name.value),
		'mail'		: encodeURIComponent (form.mail.value),
		'sendMail'	: form.sendMail.checked
	}

	http_post (EVENT_ID + '/invite', data.toJSONString ());
	detachDND ();
}

function addGroup ()
{
	form = document.getElementById ('addGroupForm');

	var data = {
		'name'		: encodeURIComponent (form.name.value)
	}

	http_post (EVENT_ID + '/addgroup', data.toJSONString ());
	detachDND ();
}

function addItem ()
{
	form = document.getElementById ('addItemForm');
	var groups = new Array ();

	for (i = 0; i < form.elements.length; i++)
	{
		var element = form.elements [i];
		if (element.type == 'checkbox' && element.checked)
		{
			groups [groups.length] = element.name;
		}
	}

	var data = {
		'name'		: encodeURIComponent (form.name.value),
		'groups'	: groups
	}

	http_post (EVENT_ID + '/adddate', data.toJSONString ());
	detachDND ();
}

function clone ()
{
	form = document.getElementById ('clonePLANForm');

	var data = {
		'expires'	: form.expires.value,
		'cloneDates'	: form.cloneDates.checked,
		'clonePeople'	: form.clonePeople.checked,
		'cloneStatus'	: form.cloneStatus.checked
	}

	http_post (EVENT_ID + '/clone', data.toJSONString ());
}

function switchOrientation()
{
	detachDND ();
	http_get (EVENT_ID + '/switch');
}

function updateRadioGroup (id, active)
{
	var items = [ 'ok', 'no', 'maybe' ];
	for (i=0; i<=2; i++) {
		var element = document.getElementById ('img' + id + '-' + items[i]);
		if (items [i] == active) {
			element.src = 'img/' + items [i] + '.gif';
		}
		else
		{
			element.src = 'img/' + items [i] + '-not.gif';
		}
	}
}

function restorePerson ()
{
	if (editPerson.id > -1)
	{
		var cell;

		cell = document.getElementById ('person-' + editPerson.id + '-name');
		cell.innerHTML = editPerson.title;

		cell = document.getElementById('controls-' + editPerson.id);
		cell.innerHTML = '';

		for (i = 0; i < editPerson.cells.length; i++)
		{
			var data = editPerson.cells [i];
			cell = document.getElementById (data.id);
			cell.innerHTML = data.html;
			cell.className = data.cssClass;
		}
		editPerson.id = -1;
	}
}

function storePerson (uid)
{
	editPerson.id = uid;
	editPerson.title = document.getElementById ('person-' + uid + '-name').innerHTML;
	editPerson.cells = [];
	// The rest of the data is stored when the new person's data arrives
}

function clearStoredPerson ()
{
	if (editPerson.id > -1)
	{
		editPerson.id = -1;
		editPerson.title = '';
		editPerson.cells = [];
	}
}

function editStatus (personID)
{
	restorePerson ();
	storePerson (personID);
	http_get (EVENT_ID + '/editstatus/' + personID);
	scheduledFocus = 'personName';
}

function saveTitle()
{
	var input = document.getElementById ('editPLANMainInput');
	var title = input.value;
	
	var data = {
		'title'		: encodeURIComponent (title)
	}

	http_post (EVENT_ID + '/title', data.toJSONString ());
}

function closeAllDateTitleForms ()
{
	for (i=0; i < openDateTitles.length; i++)
	{
		var element = openDateTitles [i];
		var target = document.getElementById (element.id);
		target.innerHTML = element.html;
		openDateTitles.shift ();
	}
}

function clearDateTitles()
{
	for (i=0; i < openDateTitles.length; i++)
	{
		openDateTitles.shift ();
	}
}

function editDate (dateID)
{
	save ('dateTitle' + dateID);
	hideHandle (dateID);
	hideElement ('date' + dateID + 'link');
	showElement ('date' + dateID + 'input');
	scheduledFocus = 'date' + dateID + 'input';
	apply_focus ();
}

function updateCloneStatusBox ()
{
	form = document.getElementById ('clonePLANForm');
	form.cloneStatus.disabled = !form.cloneDates.checked || !form.clonePeople.checked;
	form.cloneStatus.checked = form.cloneStatus.checked && !form.cloneStatus.disabled;
}

function updateOwnerMailEntry ()
{
	form = document.getElementById ('cloneForm');
	form.owner.disabled = form.cloneOwner.checked;
}

function saveDate (dateID)
{
	clearDateTitles ();
	form = document.getElementById ('date' + dateID + 'Form');

	var title = encodeURIComponent (form.date.value);
	if ((title == "") && !confirm ('Do you really wish to delete this item?'))
	{
		return;
	}

	var data = {
		'name'		: title
	}

	detachDND ();
	http_post ('dates/' + dateID, data.toJSONString ());
}

function updateInviteFormAddressEntry ()
{
	var checkbox = document.getElementById ('addPersonInvite');
	var input = document.getElementById ('addPersonMailInput');
	input.disabled = !checkbox.checked;
}

function moveTo (srcDateID, dstDateID)
{
	var data = {
		'positionOf' : dstDateID
	}

	http_post ('dates/' + srcDateID, data.toJSONString())
}

function addToGroup (dateID, groupID)
{
	var data = {
		'groupID' : groupID
	}

	http_get ('group/' + groupID + '/add/' + dateID);
}

// Form submission

function getSelectedStatus (dateID)
{
	var input;
	var values = [ 	{ 'field' : 'ok',	'value' : 1 },
			{ 'field' : 'no',	'value' : 2 },
			{ 'field' : 'maybe',	'value' : 3 } ];

	for (var i=0; i < values.length; i++)
	{
		var spec = values [i];
		input = document.getElementById
					('status' + dateID + '-' + spec.field);
		if (input.checked)
		{
			return spec.value;
		}
	}

	return 4;
}

function submitStatusForm ()
{
	var elements = new Array ();

	for (i = 0; i < editPerson.cells.length; i++)
	{
		var data = editPerson.cells [i];
		var date = data.id.substr (data.id.indexOf ('d') + 1);
		var status = getSelectedStatus (date);
		elements [elements.length] =
		{ 
			'date'	: date,
			'status': status
		};
	}

	var name = document.getElementById ('personName').value;

	if ((name == "") && !confirm ('Do you really wish to delete this person?'))
	{
		return;
	}
	var data =
	{
		'user_id' 	: editPerson.id,
		'name' 		: encodeURIComponent(name),
		'status'	: elements
	};
	detachDND ();
	http_post (EVENT_ID, data.toJSONString ());
	clearStoredPerson ();
}

// Form validation

function validateRegistrationForm ()
{
	var error = false;
	var fields = [ 'eventName', 'userName' ];

	for (i=0; i < fields.length; i++)
	{
		var fieldName = fields [i];
		var field = document.getElementById (fieldName);
		if (field.value == '')
		{
			field = document.getElementById (fieldName + 'Warning');
			field.className = 'warn';
			field.innerHTML = 'Please complete this field';
			error = true;
		}
		else
		{
			field = document.getElementById (fieldName + 'Warning');
			field.innerHTML = '';
		}
	}

	return !error;
}

// Drag & drop

function attachDND ()
{
	var dates = eval (document.getElementById ('available-dates').value);

	for (var i=0; i < dates.length; i++)
	{
		var date = dates [i];
		var handle = document.getElementById ('handle' + date);

		if (!handle) continue;

		new Draggable ('dateTitle' + date,
		{
			revert		: true,
			handle		: handle
		});
		Droppables.add ('dateTitle' + date,
		{
			accept: 'dateTitle',
			hoverclass: 'hovering',
			onDrop: function (element, droppable)
			{
				var dateID = element.id.substr (9);
				var dstID = droppable.id.substr (9);
				detachDND ();
				moveTo (dateID, dstID);
			}
		});
	}
	
	var groups = eval (document.getElementById ('available-groups').value);

	for (i=0; i < groups.length; i++)
	{
		var group = groups [i];
		if (!group) continue;

		Droppables.add ('group' + group,
		{
			accept: 'dateTitle',
			hoverclass: 'hovering',
			onDrop: function (element, droppable)
			{
				var dateID = element.id.substr (9);
				var dstID = droppable.id.substr (5);
				detachDND ();
				addToGroup (dateID, dstID);
			}
		});
	}
}

function detachDND ()
{
	var dates = eval (document.getElementById ('available-dates').value);
	
	for (i=0; i < dates.length; i++)
	{
		Droppables.remove ('dateTitle' + dates [i]);
	}

	var groups = eval (document.getElementById ('available-groups').value);

	for (i=0; i < groups.length; i++)
	{
		Droppables.remove ('group' + groups [i]);
	}
}

function showHandle (id)
{
	var handle = document.getElementById ('handle' + id);
	if (handle)
	{
		handle.style.cursor = 'move';
		handle.className = 'handle';
	}
}

function hideHandle (id)
{
	var handle = document.getElementById ('handle' + id);
	if (handle)
	{
		handle.className = 'hiddenHandle';
	}
}

function handlePersonNameKey (event)
{
	if (event)
	{
		switch (event.keyCode)
		{
			case 13:
				submitStatusForm ();
				return false;
				break;
			case 27:
				restorePerson ();
				break;
		}
	}
	return true;
}

function closeOnEscape (event, elementID)
{
	if (event && event.keyCode == 27)
	{
		detachDND ()
		var element = document.getElementById (elementID);
		restore (elementID);
		attachDND ();
	}
}

function closeNaviOnEscape (event)
{
	if (event && event.keyCode == 27)
	{
		closeNavItem (openMenuItem);
	}
}

function toggleNavItem (item)
{
	if (openMenuItem == item)
	{
		closeNavItem (item);
	} else {
		if (openMenuItem != '') closeNavItem (openMenuItem);
		showElement (item + 'Body');
		showOpened (item);
		openMenuItem = item;
	}
}

function closeNavItem (item)
{
	hideElement (item + 'Body');
	showClosed (item);
	openMenuItem = '';
}

function showOpened (menuItem)
{
	var element = document.getElementById (menuItem + 'Gfx');
	element.src = 'img/close.gif';

	scheduledFocus = menuItem + 'MainInput';
	apply_focus ();
}

function showClosed (menuItem)
{
	var element = document.getElementById (menuItem + 'Gfx');
	element.src = 'img/open.gif';

	hideElement (menuItem + 'Error');
}

function toggleCal (name)
{
	var element = document.getElementById (name + 'Cal');

	if (element.style.display == 'none')
	{
		showElement(name + 'Cal');
	}
	else
	{
		hideElement(name + 'Cal');
	}
}

function addItemDateChanged (calendar)
{
	dateChanged ('Item', calendar);
}

function addGroupDateChanged (calendar)
{
	dateChanged ('Group', calendar);
}

function dateChanged (item, calendar)
{
	if (calendar.dateClicked)
	{
		var months = new Array ('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		var days = new Array ('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');

		var date = calendar.date;

		var month = months [date.getMonth ()];
		var day = days [date.getDay ()];
		setValue ('add' + item + 'MainInput', day + ', ' + month + ' ' + date.getDate ());
	}
}
