#
# Copyright (C) 2009 Stefan Ott, all rights reserved
#

from django.template import Context, loader
from django.http import HttpResponse
from django.shortcuts import render_to_response
from planster.main.models import Plan
from django.template import RequestContext
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
	plan = Plan.objects.get(hash=plan_id)
	response = render_to_response('plan.html', {'plan': plan},
		context_instance = RequestContext(request))
	response['Cache-Control'] = 'no-cache'
	return response
