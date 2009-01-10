import os
import cgi
import logging
import wsgiref.handlers
from planster import *
from django.utils import simplejson

class NoSuchItemException(Exception):
	pass

class PlansterRPCHandler(webapp.RequestHandler):
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
		else:
			super(PlansterRPCHandler, self).handle_exception(
				exception, debug_mode)

class PlanTitleRPC(PlansterRPCHandler):
	def get(self):
		plan = self.get_plan_from_url()
		self.response.out.write(plan.title);

	def post(self):
		plan = self.get_plan_from_url()
		title = self.request.get('title')

		if title and title is not plan.title:
			plan.title = cgi.escape(title)
			plan.put()

		plan = Plan.get(plan.key())
		self.response.out.write(plan.title);

class PlanSettingsRPC(PlansterRPCHandler):
	def get(self):
		plan = self.get_plan_from_url()

		vars = {
			'plan': plan
		}

		self.render_template('settings-form.html', vars)

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

class PlanOptionsRPC(PlansterRPCHandler):
	def get_option_from_url(self):
		key = self.request.path.split('/')[4]
		option = Option.get(key)
		if option is None:
			raise NoSuchItemException()
		return option

	def delete(self):
		option = self.get_option_from_url()
		option.delete()

	def put(self):
		plan = self.get_plan_from_url()
		data = self.request.get("data")
		args = simplejson.loads(data)
		title = cgi.escape(args['title'])

		saved = plan.add_option(title)
		option = Option.get(saved.key())

		json = simplejson.dumps({
			'title': option.name,
			'id': str(option.key())
		})

		self.response.out.write(json)

	def post(self):
		# prototype tunnels DELETE and PUT requests via POST
		method = self.request.get('_method')
		if method == 'delete':
			self.delete()
			return
		elif method == 'put':
			self.put()
			return

		self.error(405)

def main():
	application = webapp.WSGIApplication([
		('/rpc/[a-zA-Z0-9]{12}/title', PlanTitleRPC),
		('/rpc/[a-zA-Z0-9]{12}/instructions', PlanInstructionsRPC),
		('/rpc/[a-zA-Z0-9]{12}/settings', PlanSettingsRPC),
		('/rpc/[a-zA-Z0-9]{12}/options.*', PlanOptionsRPC),
		])
	wsgiref.handlers.CGIHandler().run(application)

if __name__ == '__main__':
  main()
