#
# Copyright (C) 2009 Stefan Ott, all rights reserved
#
from django.http import HttpResponse, HttpResponseBadRequest
from django.utils import simplejson
from planster.main.models import *
import datetime
import cgi

# TODO: remove
import sys
#print >>sys.stdout, data
#print >>sys.stdout, request.POST

class HttpResponseCreated(HttpResponse):
	status_code = 201

	def __init__(self, url):
		HttpResponse.__init__(self)
		self['location'] = url

class PlansterRPCHandler(object):
	def __handle_data(self, method, data):
		try:
			args = simplejson.loads(data)
			return method(args)
		except ValueError:
			return HttpResponseBadRequest()

	def handle(self, request):
		if request.method == 'PUT':
			data = request.raw_post_data
			return self.__handle_data(self.put, data)
		elif request.method == 'POST':
			if ('_method' in request.POST and
				request.POST['_method'].upper() == 'PUT'):
				data = request.POST['data']
				return self.__handle_data(self.put, data)
			else:
				data = request.raw_post_data
				return self.__handle_data(self.post, data)

		elif request.method == 'GET':
			return self.get()
		else:
			return HttpResponseBadRequest()


class PlansterPlansRPCHandler(PlansterRPCHandler):
	def put(self, args):
		plan = Plan()
		plan.expires = datetime.date.today()

		if 'title' in args:
			plan.title = args['title']
		if 'instructions' in args:
			plan.instructions = args['instructions']

		plan.save()
		response = HttpResponseCreated('/rpc/%s' % plan.hash)

		data = {
			'id': plan.hash,
			'title': plan.title,
			'instructions': plan.instructions,
		}
		response.content = simplejson.dumps(data)
		return response

class PlansterAttributeRPCHandler(PlansterRPCHandler):
	def __init__(self, plan_hash):
		self.plan = Plan.objects.get(hash=plan_hash)

class PlansterPlanRPCHandler(PlansterAttributeRPCHandler):
	def __clean(self, string):
		return cgi.escape(string).replace("\n", "<br />\n")

	def post(self, args):
		if 'title' in args:
			self.plan.title = self.__clean(args['title'])
			self.plan.save()
		if 'instructions' in args:
			self.plan.instructions = self.__clean(
					args['instructions'])
			self.plan.save()

		return self.get()

	def get(self):
		return HttpResponse(simplejson.dumps({
			'id': self.plan.hash,
			'title': self.plan.title,
			'instructions': self.plan.instructions,
		}))

class PlansterOptionsRPCHandler(PlansterAttributeRPCHandler):
	def put(self, args):
		if not 'title' in args:
			return HttpResponseBadRequest()

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

class PlansterOptionRPCHandler(PlansterAttributeRPCHandler):
	def __init__(self, plan_hash, option_id):
		super(PlansterOptionRPCHandler, self).__init__(plan_hash)
		self.option = Option.objects.get(id=option_id)

	def post(self, args):
		if 'title' in args:
			self.option.name = args['title']
			self.option.save()

		return self.get()

	def get(self):
		return HttpResponse(simplejson.dumps({
			'title': self.option.name,
			'id': self.option.id
		}))

class PlansterPeopleRPCHandler(PlansterAttributeRPCHandler):
	def put(self, args):
		if not 'name' in args:
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

class PlansterPersonRPCHandler(PlansterAttributeRPCHandler):
	def __init__(self, plan_hash, person_id):
		super(PlansterPersonRPCHandler, self).__init__(plan_hash)
		self.person = Participant.objects.get(id=person_id)

	def post(self, args):
		if 'name' in args:
			self.person.name = args['name']
			self.person.save()

		content = simplejson.dumps({
			'name': self.person.name,
			'id': self.person.id
		})
		return HttpResponse(content)

	def get(self):
		return HttpResponse(simplejson.dumps({
			'name': self.person.name,
			'id': self.person.id
		}))

class PlansterResponsesRPCHandler(PlansterAttributeRPCHandler):
	def __init__(self, plan_hash, person_id):
		super(PlansterResponsesRPCHandler, self).__init__(plan_hash)
		self.person = Participant(id=person_id)

	def post(self, args):
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

def plans(request):
	handler = PlansterPlansRPCHandler()
	return handler.handle(request)

def plan(request, plan_hash):
	handler = PlansterPlanRPCHandler(plan_hash)
	return handler.handle(request)

def options(request, plan_hash):
	handler = PlansterOptionsRPCHandler(plan_hash)
	return handler.handle(request)

def option(request, plan_hash, option_id):
	handler = PlansterOptionRPCHandler(plan_hash, option_id)
	return handler.handle(request)

def people(request, plan_hash):
	handler = PlansterPeopleRPCHandler(plan_hash)
	return handler.handle(request)

def person(request, plan_hash, person_id):
	handler = PlansterPersonRPCHandler(plan_hash, person_id)
	return handler.handle(request)

def responses(request, plan_hash, person_id):
	handler = PlansterResponsesRPCHandler(plan_hash, person_id)
	return handler.handle(request)
