var responseElement;

function toggleOptions() {
	$('options').toggle();
	$('padding').toggle();

	$('logo').src = $('options').visible() ? 'logo-dark.png' : 'logo.png';
	$('toolbar').className = $('options').visible() ? 'open' : 'closed';
}

function editTitle() {
	$('title').hide();
	$('titleForm').show();
	$('titleInput').activate();
}

function editInstructions() {
	$('instructionsText').hide();
	$('instructionsForm').show();
	$('instructionsInput').activate();
}

function askResponse(element) {
	responseElement = element;

	$('responsePopup').clonePosition(element, {
		'setWidth': false,
		'setHeight': false,
		'offsetLeft': 50,
		'offsetTop': -70
	});

	$('responseSelector').hide();
	$('responsePopup').show();

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

function closeResponsePopup() {
	$('responsePopup').hide();
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

function askNewItem() {
	var height = $('newItemPopup').getHeight();

	$('newItemPopup').clonePosition($('addItemLink'), {
		'setWidth': false,
		'setHeight': false,
		'offsetLeft': 200,
		'offsetTop': 7 - height
	});
	$('newItemPopup').show();
	$('addItemInput').activate();
}

function closeAddItemPopup() {
	$('newItemPopup').hide();
}

function toggleCalendar() {
	$('calendar').toggle();
	$('showCalendarLink').toggle();
	$('hideCalendarLink').toggle();
	askNewItem()
}

function addItemDateChanged(calendar) {
	var months = new Array ('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	var days = new Array ('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');

	var date = days[calendar.date.getDay()] + ', ' +months[calendar.date.getMonth()] + ' ' + calendar.date.getDate() + ' ' + calendar.date.getFullYear();
	$('addItemInput').value = date;
}
