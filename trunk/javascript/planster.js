var needRefresh = false;
var PLAN;

function refreshIfNeeded()
{
	if (needRefresh)
	{
		window.location.reload();
	}
}

function add_plan_option(title, id)
{
	var text = document.createTextNode(title);

	var span = document.createElement('span');
	span.id = id + '-text';
	span.appendChild(text)

	var input = document.createElement('input');
	input.type = 'checkbox';
	input.name = 'delete';
	input.value = id;

	var li = document.createElement('li');
	li.className = 'plan_option';
	li.id = id;
	li.appendChild(input);
	li.appendChild(span);

	$('planOptions').appendChild(li);
}

function delete_selected_options(form)
{
	var victims = new Array();

	for (var i = 0; i < form.elements.length; i++)
	{
		var element = form.elements[i];
		if (!element)
			continue;
		if (element.type != 'checkbox')
			continue;
		if (element.checked)
			victims.push(element);
	}
	if (victims.length < 1)
	{
		alert('No options selected');
		return;
	}
	if (confirm("Really delete " + victims.length + " item(s)?"))
	{
		for (var i = 0; i < victims.length; i++)
		{
			victim = victims[i];
			var id = victim.value;
			http_delete('/rpc/' + PLAN + '/options/' + id, id,
				delete_option_success, rpc_fail);
		}
	}
}

function delete_option_success(id)
{
	$(id).checked = false;
	$('planOptions').removeChild($(id));
	Modalbox.resizeToContent();
	needRefresh = true;
}

function submit_option_success(result)
{
	var id = result.id;
	var title = result.title;
	add_plan_option(title, id);
	Modalbox.resizeToContent();
	needRefresh = true;
}

function rpc_fail()
{
	alert("D'oh! Some error occurred");
}

function submit_option(form)
{
	var title = form.name.value;
	form.name.value = '';

	if (/^ *$/.match(title))
	{
		alert('Please enter a title for your new option');
		return;
	}

	var data = {
		'title': title
	}

	var url = '/rpc/' + PLAN + '/options';
	http_put(url, data, submit_option_success, rpc_fail);
}
