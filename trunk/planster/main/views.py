#
# Copyright (C) 2009 Stefan Ott, all rights reserved
#

from django.template import Context, loader
from django.http import HttpResponse
from django.shortcuts import render_to_response
from django.utils import simplejson as json
from planster.main.models import Plan
from django.template import RequestContext
from django.contrib.auth.decorators import user_passes_test
from planster.djaptcha.examples import generate_sum_captcha

def index(request):
	plan = Plan()
	req = generate_sum_captcha()
	return render_to_response('welcome.html', {'plan': plan, 'req': req},
		context_instance = RequestContext(request))

def create(request):
	plan = Plan()
	return render_to_response('create.html', {'plan': plan},
		context_instance = RequestContext(request))

def plan(request, plan_id):
	plan = Plan.objects.get(hash=plan_id)
	response = render_to_response('plan.html', {'plan': plan},
		context_instance = RequestContext(request))
	response['Cache-Control'] = 'no-cache'
	return response

def profile(request):
	return render_to_response('profile.html',
		context_instance = RequestContext(request))

@user_passes_test(lambda u: u.is_superuser)
def export(request):
	plans = Plan.objects.all()

	meta = {'application': 'planster',
		'schema': 1}
	data = []

	for plan in plans:
		item = {
			'hash': plan.hash,
			'title': plan.title,
			'instructions': plan.instructions,
			'created': plan.created.isoformat(),
			'expires': plan.expires.isoformat(),
			'owner': plan.owner,
			'count_type': plan.count_type,
			'people': [],
			'options': [],
			'responses': []
		}
		for person in plan.people:
			item['people'].append({
				'id': person.id,
				'name': person.name
			})
			for option in plan.options:
				item['responses'].append({
					'person': person.id,
					'option': option.id,
					'response': person.getResponse(option)
				})
		for option in plan.options:
			item['options'].append({
				'id': option.id,
				'name': option.name,
				'category': option.category
			})
		data.append(item)

	result = {'meta': meta, 'data': data}
	return HttpResponse(json.dumps(result))
