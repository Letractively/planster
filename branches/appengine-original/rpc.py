import os
import cgi
import string
import logging
import wsgiref.handlers
from planster import *
from random import choice
from django.utils import simplejson
from google.appengine.api import users
from google.appengine.api.users import *

class NoSuchItemException(Exception):
	pass

class PlansterRPCHandler(webapp.RequestHandler):
	def render(self, data):
		accept = self.request.headers['accept']

		if accept == "application/json":
			self.renderJSON(data)
		else:
			self.renderHTML(data)

	def renderJSON(self, data):
		self.error(406) # not acceptable

	def renderHTML(self, data):
		self.error(406) # not acceptable

	def get_plan_from_url(self):
		key = self.request.path.split('/')[2]
		plan = Plan.get_by_key_name(key)
		if plan is None:
			raise NoSuchItemException()
		return plan

	def render_template(self, file, values):
		filepath = os.path.dirname(__file__)
		path = os.path.join(filepath, 'templates', file)
		self.response.out.write(template.render(path, values))

	def handle_exception(self, exception, debug_mode):
		if isinstance(exception, NoSuchItemException):
			self.error(404)
		elif isinstance(exception, PermissionDeniedException):
			self.error(403)
		else:
			super(PlansterRPCHandler, self).handle_exception(
				exception, debug_mode)

	def add_user_to_template(self, values):
		user = users.get_current_user()  
		values['user'] = user

class PlanRPC(PlansterRPCHandler):
	def renderJSON(self, plan):
		data = {
			'id': str(plan),
			'title': plan.title,
			'key': str(plan.key())
		}
		self.response.out.write(simplejson.dumps(data))

	def renderHTML(self, plan):
		self.redirect('/' + str(plan))

	def put(self):
		" TODO: Most of this should be in the Plan class "
		data = self.request.body
		args = simplejson.loads(data)

		while True:
			id = choice(string.letters)
			id += ''.join([choice(string.letters + string.digits)
				for i in range(11)])
			plan = Plan(key_name=id)
			if not plan.exists():
				break

		if 'title' in args:
			title = args['title']
		else:
			title='Unnamed PLAN'

		plan = Plan(key_name=id, title=title)
		plan.put()

		self.response.set_status(201) # created

		url = '/rpc/%s' % (str(plan))
		self.response.headers.add_header('Location', url)

		self.render(plan)

class PlanTitleRPC(PlansterRPCHandler):
	def render(self, plan):
		self.response.out.write(plan.title);

	def get(self):
		plan = self.get_plan_from_url()
		self.render(plan)

	def post(self):
		plan = self.get_plan_from_url()
		title = self.request.get('title')

		if title and title is not plan.title:
			plan.title = cgi.escape(title)
			plan.put()

		plan = Plan.get(plan.key())
		self.render(plan)

class PlanOwnerRPC(PlansterRPCHandler):
	def renderJSON(self, plan):
		json = simplejson.dumps({
			'owner': str(plan.owner.nickname()),
		})
		self.response.out.write(json)

	def get(self):
		user = users.get_current_user()  
		if not user:
			self.error(401) # unauthorized
			return

		plan = self.get_plan_from_url()
		self.render(plan)

	" not sure 'put' is the right method for this but what the hell "

	def put(self):
		plan = self.get_plan_from_url()

		if plan.owner:
			self.error(409) # conflict
			return

		user = users.get_current_user()  
		if not user:
			self.error(401) # unauthorized
			return

		try:
			args = simplejson.loads(self.request.body)
		except ValueError, e:
			logging.error(e)
			self.error(400) # bad request
			return

		if not 'key' in args:
			logging.error('No "key" attribute')
			self.error(400) # bad request
			return
		
		key = args['key']
		if not key == str(plan.key()):
			self.error(403) # forbidden
			return

		plan.owner = user
		plan.put()

		self.render(plan)

	def post(self):
		# prototype tunnels DELETE and PUT requests via POST
		method = self.request.get('_method')

		if method == 'put':
			data = self.request.get('data')
			self.request.body = data.encode('utf8')
			self.put()
			return

		self.error(405)

class PlanInstructionsRPC(PlansterRPCHandler):
	def get(self):
		plan = self.get_plan_from_url()
		self.response.out.write(plan.instructions)

	def post(self):
		plan = self.get_plan_from_url()
		instructions = self.request.get('instructions')

		if instructions and instructions is not plan.instructions:
			plan.instructions = cgi.escape(instructions).replace(
				"\n", "<br />\n")
			plan.put()

		plan = Plan.get(plan.key())
		self.response.out.write(plan.instructions);

class PlanPermissionsRPC(PlansterRPCHandler):
	def post(self):
		user = users.get_current_user()  
		plan = self.get_plan_from_url()
		if not user:
			self.error(401) # unauthorized
			return

		if not user == plan.owner and not users.is_current_user_admin():
			logging.error(plan.owner)
			logging.error(user)
			self.error(401) # unauthorized
			return

		data = self.request.get("data")
		args = simplejson.loads(data)
		lock_settings = args['lock_settings']
		lock_participants = args['lock_participants']

		plan.settings_locked = lock_settings
		plan.participants_locked = lock_participants

		plan.put()

		json = simplejson.dumps({
			'settings_locked': plan.settings_locked,
			'participants_locked': plan.participants_locked
		})

		self.response.out.write(json)

class PlanOptionsRPC(PlansterRPCHandler):
	def renderJSON(self, option):
		json = simplejson.dumps({
			'title': option.name,
			'id': str(option.key())
		})
		
		self.response.out.write(json)

	def put(self):
		plan = self.get_plan_from_url()
		data = self.request.body

		args = simplejson.loads(data)
		title = cgi.escape(args['title'])

		saved = plan.add_option(title)
		option = Option.get(saved.key())

		url = '/rpc/%s/options/%s' % (str(plan), option.key())
		self.response.set_status(201) # created
		self.response.headers.add_header('Location', url)

		self.render(option)

	def get(self):
		plan = self.get_plan_from_url()
		options = plan.options

		data = []

		for option in options:
			data.append({
				'title': option.name,
				'id': str(option.key())
			})

		json = simplejson.dumps(data)
		self.response.out.write(json)

	def post(self):
		# prototype tunnels DELETE and PUT requests via POST
		method = self.request.get('_method')

		if method == 'put':
			data = self.request.get('data')
			self.request.body = data.encode('utf8')
			self.put()
			return

		self.error(405)

class PlanOptionRPC(PlansterRPCHandler):
	def renderJSON(self, item):
		json = simplejson.dumps({
			'title': item.name,
			'id': str(item.key())
		})
		
		self.response.out.write(json)
		
	def get_option_from_url(self):
		key = self.request.path.split('/')[4]
		option = Option.get(key)
		if option is None:
			raise NoSuchItemException()
		return option

	def delete(self):
		option = self.get_option_from_url()
		option.delete()
		self.response.set_status(204) # no content

	def get(self):
		option = self.get_option_from_url()
		self.render()

	def post(self):
		# prototype tunnels DELETE and PUT requests via POST
		method = self.request.get('_method')

		if method == 'delete':
			self.delete()
			return

		self.error(405)

"""
	These form handlers should probably be moved to some other file
	Also, they're all pretty much identical. Should fix that.
"""

class PlanOptionsFormRPC(PlansterRPCHandler):
	def get(self):
		plan = self.get_plan_from_url()

		vars = {
			'plan': plan
		}

		self.add_user_to_template(vars)
		self.render_template('settings-form.html', vars)

class PlanFormRPC(PlansterRPCHandler):
	def get(self):
		plan = self.get_plan_from_url()

		vars = {
			'plan': plan
		}

		self.add_user_to_template(vars)
		self.render_template('plan-form.html', vars)

class ClaimPlanFormRPC(PlansterRPCHandler):
	def get(self):
		plan = self.get_plan_from_url()

		vars = {
			'plan': plan
		}

		self.add_user_to_template(vars)
		self.render_template('plan-claim.html', vars)

class PlanPermissionsFormRPC(PlansterRPCHandler):
	def get(self):
		plan = self.get_plan_from_url()

		vars = {
			'plan': plan
		}

		self.add_user_to_template(vars)
		self.render_template('plan-permissions.html', vars)

class AddPersonFormRPC(PlansterRPCHandler):
	def get(self):
		plan = self.get_plan_from_url()

		vars = {
			'plan': plan
		}

		self.add_user_to_template(vars)
		self.render_template('plan-addperson.html', vars)

def main():
	application = webapp.WSGIApplication([
		('/rpc/', PlanRPC),
		('/rpc/[a-zA-Z0-9]{12}/title', PlanTitleRPC),
		('/rpc/[a-zA-Z0-9]{12}/owner', PlanOwnerRPC),
		('/rpc/[a-zA-Z0-9]{12}/instructions', PlanInstructionsRPC),
		('/rpc/[a-zA-Z0-9]{12}/options', PlanOptionsRPC),
		('/rpc/[a-zA-Z0-9]{12}/options/.*', PlanOptionRPC),
		('/rpc/[a-zA-Z0-9]{12}/permissions', PlanPermissionsRPC),
		('/forms/[a-zA-Z0-9]{12}', PlanFormRPC),
		('/forms/[a-zA-Z0-9]{12}/claim', ClaimPlanFormRPC),
		('/forms/[a-zA-Z0-9]{12}/addperson', AddPersonFormRPC),
		('/forms/[a-zA-Z0-9]{12}/options', PlanOptionsFormRPC),
		('/forms/[a-zA-Z0-9]{12}/permissions', PlanPermissionsFormRPC),
		])
	wsgiref.handlers.CGIHandler().run(application)

if __name__ == '__main__':
  main()