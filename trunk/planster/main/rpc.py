#
# Copyright (C) 2009 Stefan Ott, all rights reserved
#
from django.http import HttpResponse, HttpResponseBadRequest
from django.utils import simplejson
from planster.main.models import *
import datetime
import cgi

class HttpResponseCreated(HttpResponse):
	status_code = 201

	def __init__(self, url):
		HttpResponse.__init__(self)
		self['location'] = url

class PlansterRPCHandler(object):
	def handle(self, request):
		if request.method == 'PUT':
			data = request.raw_post_data
			return self.put(data)
		elif request.method == 'POST':
			if ('_method' in request.POST and
				request.POST['_method'].upper() == 'PUT'):
				data = request.POST['data']
				return self.put(data)
			else:
				#return self.post(request.POST)
				#return self.post(request.raw_post_data)
				return self.post(request)

		elif request.method == 'GET':
			return self.get()
		else:
			return HttpResponseBadRequest()


class PlansterPlanRPCHandler(PlansterRPCHandler):
	def put(self, data):
		args = simplejson.loads(data)
		plan = Plan()
		plan.expires = datetime.date.today()

		if 'title' in args:
			plan.title = args['title']

		plan.save()
		response = HttpResponseCreated('/rpc/%s' % plan.hash)

		data = {
			'id': plan.hash,
			'title': plan.title,
			'key': plan.hash,
			# remove 'key'
		}
		response.content = simplejson.dumps(data)
		return response

class PlansterAttributeRPCHandler(PlansterRPCHandler):
	def __init__(self, plan_hash):
		self.plan = Plan.objects.get(hash=plan_hash)

class PlansterItemsRPCHandler(PlansterAttributeRPCHandler):
	def put(self, data):
		args = simplejson.loads(data)
		title = args['title']

		item = Option(name=title, plan=self.plan)
		item.save()

		response = HttpResponseCreated('/rpc/%s/options/%s' % (
			self.plan.hash, item.id))

		data = {
			'id': str(item.id),
			'title': item.name
		}
		response.content = simplejson.dumps(data)
		return response

	def get(self):
		options = Option.objects.filter(plan=self.plan)

		data = []
		for option in options:
			data.append({
				'title': option.name,
				'id': str(option.id)
			})

		json = simplejson.dumps(data)

		return HttpResponse(simplejson.dumps(data))

class PlansterInstructionsRPCHandler(PlansterAttributeRPCHandler):
	def get(self):
		return HttpResponse(self.plan.instructions)

	def post(self, request):
		data = request.POST
		instructions = data['instructions']

		self.plan.instructions = cgi.escape(instructions).replace(
			"\n", "<br />\n")
		self.plan.save()

		return HttpResponse(self.plan.instructions)

class PlansterTitleRPCHandler(PlansterAttributeRPCHandler):
	def get(self):
		return HttpResponse(self.plan.title)

	def post(self, request):
		data = request.POST
		title = data['title']

		self.plan.title = cgi.escape(title).replace("\n", "<br />\n")
		self.plan.save()

		return HttpResponse(self.plan.title)

class PlansterPeopleRPCHandler(PlansterAttributeRPCHandler):
	def put(self, data):
		try:
			args = simplejson.loads(data)
		except:
			return HttpResponseBadRequest()

		if not 'name' in data:
			return HttpResponseBadRequest()

		name = args['name']

		person = Participant(name=name, plan=self.plan)
		person.save()

		response = HttpResponseCreated('/rpc/%s/people/%d' % (
			self.plan.hash, person.id))
		response.content = simplejson.dumps({
			'name': person.name,
			'id': person.id
		});
		return response

	def get(self):
		data = []
		for person in self.plan.people:
			data.append({
				'name': person.name,
				'id': person.id
			})
		return HttpResponse(simplejson.dumps(data))

class PlansterResponsesRPCHandler(PlansterAttributeRPCHandler):
	def __init__(self, plan_hash, person_id):
		super(PlansterResponsesRPCHandler, self).__init__(plan_hash)
		self.person = Participant(id=person_id)

	def post(self, request):
		data = request.raw_post_data
		args = simplejson.loads(data)

		for option in args:
			value = args[option]
			self.person.setResponse(Option(id=option), value)

		return self.get()

	def get(self):
		data = Response.objects.filter(participant=self.person)

		responses = {}
		for item in data:
			responses[item.option.id] = item.value
		return HttpResponse(simplejson.dumps(responses))

"""class PlansterOwnerRPCHandler(PlansterRPCHandler):
	def __init__(self, plan_hash):
		self.hash = plan_hash

	def get(self):
		plan = Plan.objects.get(hash=self.hash)
		return HttpResponse(plan.owner)

	def put(self, data):
		plan = Plan.objects.get(hash=self.hash)
		print "DATA="
		print data
		return HttpResponse()"""

def plan(request):
	handler = PlansterPlanRPCHandler()
	return handler.handle(request)

"""def items(request, plan_hash):
	return HttpResponse("foo")"""

def options(request, plan_hash):
	handler = PlansterItemsRPCHandler(plan_hash)
	return handler.handle(request)

def instructions(request, plan_hash):
	handler = PlansterInstructionsRPCHandler(plan_hash)
	return handler.handle(request)

def title(request, plan_hash):
	handler = PlansterTitleRPCHandler(plan_hash)
	return handler.handle(request)

def people(request, plan_hash):
	handler = PlansterPeopleRPCHandler(plan_hash)
	return handler.handle(request)

def responses(request, plan_hash, person_id):
	handler = PlansterResponsesRPCHandler(plan_hash, person_id)
	return handler.handle(request)


"""def owner(request, plan_hash):
	handler = PlansterOwnerRPCHandler(plan_hash)
	return handler.handle(request)"""
