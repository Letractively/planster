#!/usr/bin/env python
#
# Copyright 2004-2009 Stefan Ott
#
# Licensed under the GNU GPL v2.
#

import os
import cgi
import string
import logging
import wsgiref.handlers
from random import choice
from planster import *
from google.appengine.ext import db
from google.appengine.api import users
from google.appengine.ext import webapp
from google.appengine.ext.webapp import template

template.register_template_library('templatetags.plan_extras')

class PlansterRequestHandler(webapp.RequestHandler):
	def get_plan_from_URL(self):
		key = self.request.path.split('/')[1]
		plan = Plan.get_by_key_name(key)
		return plan

	def render_template(self, file, values):
		filepath = os.path.dirname(__file__)
		path = os.path.join(filepath, 'templates', file)
		self.response.out.write(template.render(path, values))

	def add_user_to_template(self, values):
		user = users.get_current_user()  
		values['user'] = user

		if user is None:
			values['login_url'] = users.create_login_url(
				self.request.uri)
		else:
			values['logout_url'] = users.create_logout_url(
				self.request.uri)

class RegistrationPage(PlansterRequestHandler):
	def get(self):
		values = {}
		self.add_user_to_template(values)
		self.render_template('create.html', values)

	def post(self):
		while True:
			id = choice(string.letters)
			id += ''.join([choice(string.letters + string.digits)
				for i in range(11)])
			plan = Plan(key_name=id)
			if not plan.exists():
				break

		title = self.request.get('title')
		if not title:
			title='Unnamed PLAN'

		plan = Plan(key_name=id, title=title)

		user = users.get_current_user()
		if user:
			plan.owner = user

		plan.put()

		self.redirect('/' + str(plan))

class ListOfPlans(webapp.RequestHandler):
	def get(self):
		plans = Plan.all()

		self.response.out.write('<ul>')
		for plan in plans:
			self.response.out.write('<li><a href="/' + str(plan) + 
				'">' + plan.title + '</a></li>')

		self.response.out.write('</ul>')
		self.response.out.write('<div><a href="/create">new</a></div>')

class PlanView(PlansterRequestHandler):
	def get(self):
		plan = self.get_plan_from_URL()

		if plan is None:
			self.error(404)
			return

		template_values = {
			'plan': plan,
		}
		self.add_user_to_template(template_values)

		edit = self.request.get("edit")
		if edit:
			template_values['edit'] = db.Key(edit)

		add = self.request.get("add")
		if add:
			template_values['add'] = add

		self.render_template('plan.html', template_values)

class PlanPeople(PlansterRequestHandler):
	def post(self):
		plan = self.get_plan_from_URL()

		if plan is None:
			self.error(404)
			return

		name = cgi.escape(self.request.get("name"))
		submit = self.request.get("submit")

		if submit == 'Add' and name:
			plan.add_participant(name)

		self.redirect('/' + str(plan))

class PlanOptions(PlansterRequestHandler):
	def post(self):
		plan = self.get_plan_from_URL()

		if plan is None:
			self.error(404)
			return

		name = cgi.escape(self.request.get("name"))
		submit = self.request.get("submit")

		if submit == 'Add' and name:
			plan.add_option(name)

		self.redirect('/' + str(plan))

class PlanResponse(PlansterRequestHandler):
	def post(self):
		plan = self.get_plan_from_URL()

		if plan is None:
			self.error(404)
			return

		participant_id = self.request.get("participant")
		participant = Participant.get(participant_id)
		submit = self.request.get("submit")
		name = self.request.get("name")

		if submit is "cancel":
			self.redirect('/' + str(plan))

		if not name:
			participant.delete()
		else:
			if not name == participant.name:
				participant.name = name
				participant.put()
			for option in plan.options:
				option_key = str(option.key())
				value = self.request.get(option_key)
				response = Response(key_name = option_key +
					participant_id)
				response.option = option
				response.participant = participant
				if not value:
					value = 0
				response.value = int(value)
				response.put()

		self.redirect('/' + str(plan))

class WelcomePage(PlansterRequestHandler):
	def get(self):
		self.render_template('welcome.html', None)

class MainHandler(webapp.RequestHandler):
	def get(self):
		self.response.out.write('Hello world!')

def main():
	application = webapp.WSGIApplication([
		('/', WelcomePage),
		('/create', RegistrationPage),
		('/plans', ListOfPlans),
		('/[a-zA-Z0-9]{12}', PlanView),
		('/[a-zA-Z0-9]{12}/people', PlanPeople),
		('/[a-zA-Z0-9]{12}/options', PlanOptions),
		('/[a-zA-Z0-9]{12}/response', PlanResponse),
		], debug=False)
	wsgiref.handlers.CGIHandler().run(application)

if __name__ == '__main__':
  main()
