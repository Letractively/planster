var responseElement;
var activeItem;

function toggleOptions() {
	$('options').toggle();
	$('padding').toggle();

	if ($('options').visible()) {
		$('logo').src = $('logo').src.replace('logo.png', 'logo-dark.png');
	} else {
		$('logo').src = $('logo').src.replace('logo-dark.png', 'logo.png');
	}
	$('toolbar').className = $('options').visible() ? 'open' : 'closed';
}

function editTitle() {
	$('title').hide();
	$('titleInput').value = $('title').innerHTML;
	$('titleForm').show();
	$('titleInput').activate();
}

function editInstructions() {
	$('instructionsText').hide();
	$('instructionsInput').value = $('instructionsText').innerHTML;
	$('instructionsForm').show();
	$('instructionsInput').activate();
}

function setSelection(element) {
	$('responseSelector').clonePosition(element, {
		'setWidth': false,
		'setHeight': false,
		'offsetLeft': -2,
		'offsetTop': 23
	});
	$('responseSelector').show();
}

function setResponse(response) {
	closeResponsePopup();
	responseElement.childNodes[0].src = response + '.png';
}

function toggleCalendar() {
	$('calendar').toggle();
	$('showCalendarLink').toggle();
	$('hideCalendarLink').toggle();
	askItem(activeItem)
}

function itemDateChanged(calendar) {
	var months = new Array ('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	var days = new Array ('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');

	var date = days[calendar.date.getDay()] + ', ' +months[calendar.date.getMonth()] + ' ' + calendar.date.getDate() + ' ' + calendar.date.getFullYear();
	$('itemTitle').value = date;
}

function showPopup(popup, where) {
	popup.clonePosition(where, {
		'setWidth': false,
		'setHeight': false,
		'offsetLeft': where.getWidth() + 25,
		'offsetTop': where.getHeight() - popup.getHeight()
	});
	popup.show();
}

function askItem(item) {
	closeResponsePopup();
	closePersonPopup();

	activeItem = item

	if (item.parentNode.className == 'addItem') {
		$('itemTitle').value = '';
		$('editItemTitle').hide();
		$('addItemTitle').show();
	} else {
		$('itemTitle').value = item.innerHTML;
		$('editItemTitle').show();
		$('addItemTitle').hide();
	}

	showPopup($('itemPopup'), item);
	$('itemTitle').activate();
}

function askPerson(person) {
	closeResponsePopup();
	closeItemPopup();

	if (person.parentNode.className == 'addItem') {
		$('personName').value = '';
		$('editPersonTitle').hide();
		$('addPersonTitle').show();
	} else {
		$('personName').value = person.innerHTML;
		$('addPersonTitle').hide();
		$('editPersonTitle').show();
	}

	showPopup($('personPopup'), person);
	$('personName').activate();
}

function askResponse(element) {
	closePersonPopup();
	closeItemPopup();

	responseElement = element;

	$('responseSelector').hide();
	showPopup($('responsePopup'), element);

	// set the currently selected response in the popup
	var popup = $('responsePopup');
	for (var i=0; i < popup.childNodes.length; i++) {
		var child = popup.childNodes[i];
		if (child.className == 'selection') {
			for (var j=0; j < child.childNodes.length; j++) {
				var img = child.childNodes[j];
				if (img.src == element.childNodes[0].src) {
					setSelection(img);
				}
			}
		}
	}
}

function closeItemPopup() {
	$('itemPopup').hide();
}

function closePersonPopup() {
	$('personPopup').hide();
}

function closeResponsePopup() {
	$('responsePopup').hide();
}

function error() {
	alert('Something went wrong...');
}

function createPlan(form) {
	var title = form.title.value;
	var instructions = form.instructions.value;
	var expires = form.expires.value;

	var data = new Hash();

	data.set('title', title);
	data.set('instructions', instructions);
	data.set('expires', expires);

	new Ajax.Request('rpc', {
		method: 'put',
		parameters: { 'data': data.toJSON() },
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			data = response.evalJSON();
			window.location = data.id;
		},
		onFailure: function(){ error() }
	});
}

function saveTitle(form, plan) {
	var data = new Hash();
	data.set('title', form.title.value);

	new Ajax.Request('rpc/' + plan + '/title', {
		method: 'post',
		parameters: { 'title': form.title.value },
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			$('title').innerHTML = response;
			$('titleForm').hide();
			$('title').show();
		},
		onFailure: function(){ error() }
	});
}

function saveInstructions(form, plan) {
	new Ajax.Request('rpc/' + plan + '/instructions', {
		method: 'post',
		parameters: { 'instructions': form.instructions.value },
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			$('instructions').innerHTML = response;
			$('instructionsForm').hide();
			$('instructions').show();
		},
		onFailure: function(){ error() }
	});
}

function savePerson(form, plan) {
}
