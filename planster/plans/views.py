#
# Copyright (C) 2009 Stefan Ott, all rights reserved
#

from django.template import Context, loader
from django.http import HttpResponse, HttpResponseRedirect, Http404
from django.shortcuts import render_to_response
from django.utils import simplejson as json
from django.template import RequestContext
from django.core.urlresolvers import reverse
from django.contrib.auth.decorators import user_passes_test, login_required
from plans.models import Plan, UserProfile
from djaptcha.examples import generate_sum_captcha

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
	try:
		plan = Plan.objects.get(hash=plan_id)
	except Plan.DoesNotExist:
		raise Http404

	user = request.user

	if user.is_authenticated():
		profile = user.get_profile()
		profile.plans.add(plan)

	response = render_to_response('plan.html', {'plan': plan},
		context_instance = RequestContext(request))
	response['Cache-Control'] = 'no-cache'
	return response

def legacy(request, plan_hash):
	response = HttpResponseRedirect('http://legacy.planster.net/%s' %
			plan_hash)
	return response

def nojs(request):
	return render_to_response('nojs.html',
		context_instance = RequestContext(request))

def server_error(request, code=500):
	response = render_to_response('%d.html' % code,
		context_instance = RequestContext(request))
	response.status_code = code
	return response

def error_404(request):
	return server_error(request, 404)

@login_required
def profile(request):
	if 'clear' in request.GET:
		profile = request.user.get_profile()
		profile.plans.clear()
		return HttpResponseRedirect(reverse('plans.views.profile'))

	return render_to_response('profile.html',
		{ 'profile': request.user.get_profile() },
		context_instance = RequestContext(request),
	)

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
