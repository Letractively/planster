var DHTML = (document.getElementById || document.all || document.layers);
var editPerson = -1;
var activeDate = -1;

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
	http.open('get', 'rpc.php?action='+action+'&'+arg, true);
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
		}
	}
}

function getDateForm(eventID) {
	var element = document.getElementById('newdate');
	element.innerHTML = '<form name="newDateForm" onsubmit="javascript:sndReq(\'adddate\', \'event=' + eventID + '&date=\' + document.newDateForm.date.value); return false;"><input id="date" size="10" /></form>';
}

function getPersonForm(eventID) {
	var element = document.getElementById('newperson');
	element.innerHTML = '<form name="newPersonForm" onsubmit="javascript:sndReq(\'addperson\', \'event=' + eventID + '&name=\' + document.newPersonForm.name.value); return false;"><input id="name" size="10" /></form>';
}

function getInviteForm(eventID) {
	sndReq('getInviteForm', 'event=' + eventID);
}

function invite(eventID) {
	name = encodeURIComponent(document.inviteForm.name.value);
	mail = encodeURIComponent(document.inviteForm.mail.value);

	sndReq('invite', 'event=' + eventID + '&name=' + name + '&mail=' + mail);
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
		var element = document['img' + id + '-' + items[i]];
		if (items[i] == active) {
			element.src = 'img/' + items[i] + '.gif';
		} else {
			element.src = 'img/' + items[i] + '-not.gif';
		}
	}
}

function editStatus(event, uid) {
	editPerson = uid;
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
}
