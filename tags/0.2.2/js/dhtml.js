var DHTML = (document.getElementById || document.all || document.layers);
var editPerson = -1;
var activeDate = -1;
var event_id;
var scheduledFocus = '';

function getObject(name) {
	if (document.getElementById) {
  		this.obj = document.getElementById(name);
		this.style = document.getElementById(name).style;
	} else if (document.all) {
		this.obj = document.all[name];
		this.style = document.all[name].style;
	} else if (document.layers) {
		this.obj = document.layers[name];
		this.style = document.layers[name];
	}
}

function createRequestObject() {
	var ro;

	try {
		ro = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			ro = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e) {
			ro = new XMLHttpRequest();
		}
	}
	return ro;
}

var http = createRequestObject();

function sndReq(action, arg) {
	http.open('get', 'rpc.php?action=' + action + '&' + arg + "&ms=" + new Date().getTime(), true);
	http.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
	http.onreadystatechange = handleResponse;
	http.send(null);
}

function handleResponse() {
	if(http.readyState == 4){
		var response = http.responseText;
		var update = new Array();

		if (response.indexOf('|' != -1)) {
			update = response.split('|');
			try {
				var element = document.getElementById(update[0]);
				element.innerHTML = update[1];
			} catch (e) {
				alert(e);
			}
			if (scheduledFocus != '') {
				var element =  document.getElementById(scheduledFocus);
				element.focus();
				scheduledFocus = '';
			}
		}
	}
}

function getDateForm(eventID) {
	scheduledFocus = 'date';
	sndReq('getAddDateForm', 'event=' + eventID);
}

function setEvent(eventID) {
	event_id = eventID;
}

// for the multiple-dates calendar
var MA = [];

function addMultipleDates(cal) {
	MA.length = 0;
	var items = [];

	for (var i in cal.multiple) {
		var item = cal.multiple[i];
		// sometimes the date is not actually selected,
		// so let's check
		if (item) {
			// OK, selected
			items[items.length] = item.print('%s');
		}
	}
	if (items.length > 0) {
		sndReq('addDates', 'event=' + event_id + '&items=' + items);
	}
	cal.hide();
	return true;
}

function getPersonForm(eventID) {
	var element = document.getElementById('newperson');
	element.innerHTML = '<form name="newPersonForm" onsubmit="javascript:sndReq(\'addperson\', \'event=' + eventID + '&name=\' + document.newPersonForm.name.value); return false;"><input id="name" size="10" /></form>';
}

function getInviteForm(eventID) {
	scheduledFocus = 'name';
	sndReq('getInviteForm', 'event=' + eventID);
}

function getCloneForm(eventID) {
	scheduledFocus = 'name';
	sndReq('getCloneForm', 'event=' + eventID);
}

function invite(eventID) {
	form = document.getElementById('inviteForm');

	name = encodeURIComponent(form.name.value);
	mail = encodeURIComponent(form.mail.value);
	sendMail = form.sendMail.checked;

	sndReq('invite', 'event=' + eventID + '&name=' + name + '&mail=' + mail + '&sendMail=' + sendMail);
}

function clone(eventID) {
	form = document.getElementById('cloneForm');
	name = encodeURIComponent(form.name.value);
	owner = encodeURIComponent(form.owner.value);
	expires = form.expires.value;
	cloneOwner = form.cloneOwner.checked;
	cloneDates = form.cloneDates.checked;
	clonePeople = form.clonePeople.checked;
	cloneStatus = form.cloneStatus.checked;

	sndReq('clone', 'event=' + eventID + '&name=' + name + '&owner=' + owner + '&cloneOwner=' + cloneOwner + '&cloneDates=' + cloneDates + '&clonePeople=' + clonePeople + '&cloneStatus=' + cloneStatus + '&expires=' + expires);
}

function hideInvite() {
	var element = document.getElementById('dialog');
	element.innerHTML = '';
}

function switchOrientation(eventID) {
	sndReq('switch_orientation', 'event=' + eventID);
}

function updateRadioGroup(id, active) {
	var items = new Array(3);
	items[0] = 'ok';
	items[1] = 'no';
	items[2] = 'maybe';
	for (i=0; i<=2; i++) {
		var element = document.getElementById('img' + id + '-' + items[i]);
		if (items[i] == active) {
			element.src = 'img/' + items[i] + '.gif';
		} else {
			element.src = 'img/' + items[i] + '-not.gif';
		}
	}
}

function editStatus(event, uid) {
	editPerson = uid;
	scheduledFocus = 'personName';
	sndReq('editstatus', 'event=' + event + '&uid=' + uid)
}

function editDate(event, date) {
	if (editPerson != -1 || activeDate != -1) {
		sndReq('editdate', 'event=' + event + '&date=' + date + "&full");
		editPerson = -1;
	} else {
		sndReq('editdate', 'event=' + event + '&date=' + date);
	}
	activeDate = date;
	scheduledFocus = 'newTitle';
}

function updateCloneStatusBox() {
	form = document.getElementById('cloneForm');
	form.cloneStatus.disabled = !form.cloneDates.checked || !form.clonePeople.checked;
	form.cloneStatus.checked = form.cloneStatus.checked && !form.cloneStatus.disabled;
}

function updateOwnerMailEntry() {
	form = document.getElementById('cloneForm');
	form.owner.disabled = form.cloneOwner.checked;
}

function saveDate(event, dateID) {
	form = document.getElementById('date' + dateID + 'Form');
	title = encodeURIComponent(form.newTitle.value);

	sndReq('savedate', 'event=' + event + '&date=' + dateID + '&title=' + title);
}

function updateInviteFormAddressEntry() {
	form = document.getElementById('inviteForm');
	form.mail.disabled = !form.sendMail.checked;
}
