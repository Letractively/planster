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
		$('itemTitle').parentNode.parentNode.id.value = '';
		$('editItemTitle').hide();
		$('addItemTitle').show();
	} else {
		$('itemTitle').value = item.innerHTML;
		$('itemTitle').parentNode.parentNode.id.value =
			item.parentNode.id.split('-')[1];
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
		$('personName').parentNode.parentNode.id.value = '';
		$('editPersonTitle').hide();
		$('addPersonTitle').show();
		$('personName').personId = -1;
	} else {
		$('personName').value = person.innerHTML;
		$('personName').parentNode.parentNode.id.value =
			person.parentNode.id.split('-')[1];
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
	var captcha_id = form.captcha_id.value;
	var captcha_value = form.captcha_value.value;

	var data = new Hash();

	data.set('title', title);
	data.set('instructions', instructions);
	data.set('expires', expires);
	data.set('captcha-id', captcha_id);
	data.set('captcha-value', captcha_value);

	new Ajax.Request('rpc', {
		method: 'put',
		parameters: { 'data': data.toJSON() },
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			data = response.evalJSON();
			window.location = data.id;
		},
		onFailure: function(transport){
			switch (transport.status)
			{
				case 400:
					alert(transport.responseText);
					form.captcha_value.activate();
					break;
				case 403:
					alert(transport.responseText);
					var url = transport.getHeader('location');
					var parts = url.split('/');
					var id = parts[parts.length - 1];

					$('captcha').src = url + '.jpg';

					form.captcha_id.value = id;
					form.captcha_value.activate();
					break;
			}
		}
	});
}

function saveTitle(form, plan) {
	var data = new Hash();
	data.set('title', form.title.value);

	new Ajax.Request('rpc/' + plan, {
		method: 'post',
		postBody: data.toJSON(),
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			data = response.evalJSON();
			$('title').innerHTML = data.title;
			$('titleForm').hide();
			$('title').show();
		},
		onFailure: function(){ error() }
	});
}

function saveInstructions(form, plan) {
	var data = new Hash();
	data.set('instructions', form.instructions.value);

	new Ajax.Request('rpc/' + plan, {
		method: 'post',
		postBody: data.toJSON(),
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			data = response.evalJSON();
			$('instructions').innerHTML = data.instructions;
			$('instructionsForm').hide();
			$('instructions').show();
		},
		onFailure: function(){ error() }
	});
}

function savePerson(form, plan) {
	var data = new Hash();
	data.set('name', form.name.value);

	var id = form.id.value;

	if (id == '')
		saveNewPerson(plan, data);
	else
		saveEditedPerson(plan, data, id);
}

function saveNewPerson(plan, data) {
	var url = 'rpc/' + plan + '/people';

	new Ajax.Request(url, {
		method: 'put',
		parameters: { 'data': data.toJSON() },
		onSuccess: function(transport){
			window.location = plan;
		},
		onFailure: function(){ error() }
	});
}

function saveEditedPerson(plan, data, id) {
	var url = 'rpc/' + plan + '/people/' + id;

	new Ajax.Request(url, {
		method: 'post',
		postBody: data.toJSON(),
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			data = response.evalJSON();
			$('person-' + id).childNodes[0].innerHTML = data.name;
			closePersonPopup();
		},
		onFailure: function(){ error() }
	});
}

function saveItem(form, plan) {
	var data = new Hash();
	data.set('title', form.title.value);

	var id = form.id.value;

	if (id == '')
		saveNewItem(plan, data);
	else
		saveEditedItem(plan, data, id);
}

function saveNewItem(plan, data) {
	new Ajax.Request('rpc/' + plan + '/options', {
		method: 'put',
		parameters: { 'data': data.toJSON() },
		onSuccess: function(transport){
			window.location = plan;
		},
		onFailure: function(){ error() }
	});
}

function saveEditedItem(plan, data, id) {
	new Ajax.Request('rpc/' + plan + '/options/' + id, {
		method: 'put',
		postBody: data.toJSON(),
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			data = response.evalJSON();
			$('option-' + id).childNodes[0].innerHTML = data.title;
			closeItemPopup();
		},
		onFailure: function(){ error() }
	});
}

function setResponse(response, plan) {
	var id = responseElement.parentNode.id.split('-');
	var person = id[1];
	var option = id[2];

	var data = new Hash();
	data.set(option, response);

	new Ajax.Request('rpc/' + plan + '/people/' + person + '/responses', {
		method: 'post',
		postBody: data.toJSON(),
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			data = response.evalJSON();

			var values = Array('unknown', 'yes', 'no', 'maybe');
			var value = values[data[option]];

			closeResponsePopup();
			responseElement.childNodes[0].src = responseElement.childNodes[0].src.gsub(/[a-z]+.png/, value + '.png');
		},
		onFailure: function(){ error() }
	});
}

