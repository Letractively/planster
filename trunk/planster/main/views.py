#
# Copyright (C) 2009 Stefan Ott, all rights reserved
#

from django.template import Context, loader
from django.http import HttpResponse
from django.shortcuts import render_to_response
from planster.main.models import Plan
from django.template import RequestContext

def index(request):
	return render_to_response('welcome.html', {},
		context_instance = RequestContext(request))

def create(request):
	plan = Plan()
	return render_to_response('create.html', {'plan': plan},
		context_instance = RequestContext(request))

def plan(request, plan_id):
	plan = Plan.objects.get(hash=plan_id)
	return render_to_response('plan.html', {'plan': plan},
		context_instance = RequestContext(request))
