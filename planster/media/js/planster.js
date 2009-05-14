var responseElement;
var activeItem;

function toggleOptions()
{
	$('options').toggle();
	$('padding').toggle();

	var logo = $('logo');

	if ($('options').visible())
	{
		logo.src = logo.src.replace('logo.png', 'logo-dark.png');
	}
	else
	{
		logo.src = logo.src.replace('logo-dark.png', 'logo.png');
	}

	$('toolbar').className = $('options').visible() ? 'open' : 'closed';
}

function editTitle()
{
	$('title').hide();
	$('titleInput').value = $('title').innerHTML;
	$('titleForm').show();
	$('titleInput').activate();
}

function editInstructions()
{
	$('instructionsText').hide();
	$('instructionsInput').value = $('instructionsText').innerHTML;
	$('instructionsForm').show();
	$('instructionsInput').activate();
}

function setSelection(element)
{
	$('responseSelector').clonePosition(element,
	{
		'setWidth': false,
		'setHeight': false,
		'offsetLeft': -2,
		'offsetTop': 23
	});
	$('responseSelector').show();
}

function showPopup(popup, where)
{
	popup.clonePosition(where,
	{
		'setWidth': false,
		'setHeight': false,
		'offsetLeft': where.getWidth() + 25,
		'offsetTop': where.getHeight() - popup.getHeight()
	});
	popup.show();
}

function closeItemPopup()
{
	$('itemPopup').hide();
}

function closePersonPopup()
{
	$('personPopup').hide();
}

function closeResponsePopup()
{
	$('responsePopup').hide();
}


function askItem(item)
{
	closeResponsePopup();
	closePersonPopup();

	activeItem = item;

	if (item.parentNode.className == 'addItem')
	{
		$('itemTitle').value = '';
		$('itemTitle').parentNode.parentNode.id.value = '';
		$('editItemTitle').hide();
		$('addItemTitle').show();
	}
	else
	{
		$('itemTitle').value = item.innerHTML;
		$('itemTitle').parentNode.parentNode.id.value =
			item.parentNode.id.split('-')[1];
		$('editItemTitle').show();
		$('addItemTitle').hide();
	}

	showPopup($('itemPopup'), item);
	$('itemTitle').activate();
}


function toggleCalendar()
{
	$('calendar').toggle();
	$('showCalendarLink').toggle();
	$('hideCalendarLink').toggle();
	askItem(activeItem);
}

function itemDateChanged(calendar)
{
	var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
			'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

	var date = days[calendar.date.getDay()] + ', ' +
		months[calendar.date.getMonth()] + ' ' +
		calendar.date.getDate() + ' ' +
		calendar.date.getFullYear();

	$('itemTitle').value = date;
}

function askPerson(person)
{
	closeResponsePopup();
	closeItemPopup();

	if (person.parentNode.className == 'addItem')
	{
		$('personName').value = '';
		$('personName').parentNode.parentNode.id.value = '';
		$('editPersonTitle').hide();
		$('addPersonTitle').show();
		$('personName').personId = -1;
	}
	else
	{
		$('personName').value = person.innerHTML;
		$('personName').parentNode.parentNode.id.value =
			person.parentNode.id.split('-')[1];
		$('addPersonTitle').hide();
		$('editPersonTitle').show();
	}

	showPopup($('personPopup'), person);
	$('personName').activate();
}

function askResponse(element)
{
	var current = element.select('img')[0];
	responseElement = element;

	closePersonPopup();
	closeItemPopup();

	$('responseSelector').hide();
	showPopup($('responsePopup'), element);

	// set the currently selected response in the popup
	$$('#responsePopup .selection img').each(function(img)
	{
		if (img.src == current.src)
		{
			setSelection(img);
		}
	});
}

function error()
{
	alert('Something went wrong...');
}

function createPlan(form)
{
	var title = $F(form.title);
	var instructions = $F(form.instructions);
	var expires = $F(form.expires);
	var captcha_id = $F(form.captcha_id);
	var captcha_value = $F(form.captcha_value);

	var data = $H({
		'title': title,
		'instructions': instructions,
		'expires': expires,
		'captcha-id': captcha_id,
		'captcha-value': captcha_value
	});

	new Ajax.Request('rpc',
	{
		method: 'put',
		parameters: { 'data': data.toJSON() },
		on201: function(transport)
		{
			var data = transport.responseJSON;
			window.location = data.id;
		},
		on400: function(transport)
		{
			alert(transport.responseText);
			form.captcha_value.activate();
		},
		on403: function(transport)
		{
			alert(transport.responseText);
			var url = transport.getHeader('location');
			var parts = url.split('/');
			var id = parts[parts.length - 1];

			$('captcha').src = url + '.jpg';

			form.captcha_id.value = id;
			form.captcha_value.activate();
		}
	});
}

function saveTitle(form, plan)
{
	var data = $H({'title': $F(form.title)});

	new Ajax.Request('rpc/' + plan,
	{
		method: 'post',
		postBody: data.toJSON(),
		onSuccess: function(transport)
		{
			var data = transport.responseJSON;
			$('title').update(data.title);
			$('titleForm').hide();
			$('title').show();
		},
		onFailure: function() { error(); }
	});
}

function getCount(plan, option)
{
	new Ajax.Request('rpc/' + plan + '/options/' + option,
	{
		method: 'get',
		onSuccess: function(transport)
		{
			var data = transport.responseJSON;
			$(data.id + '-count').update(data.count);
		},
		onFailure: function() { error(); }
	});
}

function getCounts(plan)
{
	new Ajax.Request('rpc/' + plan + '/options',
	{
		method: 'get',
		onSuccess: function(transport)
		{
			transport.responseJSON.each(function(item)
			{
				$(item.id + '-count').update(item.count);
			});
		},
		onFailure: function() { error(); }
	});
}

function hideCounts()
{
	$$('.count').invoke('hide');
}

function showCounts()
{
	$$('.count').invoke('show');
}

function setCountType(type, plan)
{
	var data = $H({'count_type': $F(type)});

	new Ajax.Request('rpc/' + plan,
	{
		method: 'post',
		postBody: data.toJSON(),
		onSuccess: function(transport)
		{
			data = transport.responseJSON;

			if (data.count_type == 1)
			{
				hideCounts();
			}
			else
			{
				getCounts(plan);
				showCounts();
			}
		},
		onFailure: function() { error(); }
	});
}

function saveInstructions(form, plan)
{
	var data = $H({'instructions': $F(form.instructions)});

	new Ajax.Request('rpc/' + plan,
	{
		method: 'post',
		postBody: data.toJSON(),
		onSuccess: function(transport)
		{
			var data = transport.responseJSON;
			$('instructions').update(data.instructions);
			$('instructionsForm').hide();
			$('instructions').show();
		},
		onFailure: function() { error(); }
	});
}

function saveEditedPerson(plan, data, id)
{
	new Ajax.Request('rpc/' + plan + '/people/' + id,
	{
		method: 'post',
		postBody: data.toJSON(),
		onSuccess: function(transport)
		{
			data = transport.responseJSON;
			$('person-' + id).childNodes[0].update(data.name);
			closePersonPopup();
		},
		onFailure: function() { error(); }
	});
}

function saveNewPerson(plan, data)
{
	new Ajax.Request('rpc/' + plan + '/people',
	{
		method: 'put',
		parameters: { 'data': data.toJSON() },
		onSuccess: function(transport)
		{
			window.location = plan;
		},
		onFailure: function() { error(); }
	});
}

function savePerson(form, plan)
{
	var data = $H({'name': $F(form.name)});
	var id = $F(form.id);

	if (id.empty())
	{
		saveNewPerson(plan, data);
	}
	else
	{
		saveEditedPerson(plan, data, id);
	}
}

function saveNewItem(plan, data)
{
	new Ajax.Request('rpc/' + plan + '/options',
	{
		method: 'put',
		parameters: { 'data': data.toJSON() },
		onSuccess: function(transport)
		{
			window.location = plan;
		},
		onFailure: function() { error(); }
	});
}

function saveEditedItem(plan, data, id)
{
	new Ajax.Request('rpc/' + plan + '/options/' + id,
	{
		method: 'put',
		postBody: data.toJSON(),
		onSuccess: function(transport)
		{
			var data = transport.responseJSON;
			$('option-' + id).childNodes[0].update(data.title);
			closeItemPopup();
		},
		onFailure: function() { error(); }
	});
}

function saveItem(form, plan)
{
	var data = $H({'title': $F(form.title)});
	var id = $F(form.id);

	if (id.empty())
	{
		saveNewItem(plan, data);
	}
	else
	{
		saveEditedItem(plan, data, id);
	}
}

function setResponse(response, plan)
{
	var id = responseElement.parentNode.id.split('-');
	var person = id[1];
	var option = id[2];

	var data = new Hash();
	data.set(option, response);

	new Ajax.Request('rpc/' + plan + '/people/' + person + '/responses',
	{
		method: 'post',
		postBody: data.toJSON(),
		onSuccess: function(transport)
		{
			var data = transport.responseJSON;
			var values = Array('unknown', 'yes', 'no', 'maybe');
			var value = values[data[option]];

			closeResponsePopup();
			responseElement.childNodes[0].src = responseElement.childNodes[0].src.gsub(/[a-z]+.png/, value + '.png');
			getCount(plan, option);
		},
		onFailure: function() { error(); }
	});
}

