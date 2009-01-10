#!/usr/bin/env python
#
# Copyright 2004-2009 Stefan Ott
#
# Licensed under the GNU GPL v2.
#

import logging
from google.appengine.ext import db
from google.appengine.ext import webapp
from google.appengine.api import memcache
from google.appengine.ext.webapp import template

class PlansterModel(db.Model):
	def get_cached_items(self, class_obj, filter_key):
		cache_key = str(self) + '_' + class_obj.__name__
		items = memcache.get(cache_key)

		if items is None:
			logging.debug("db store lookup for %s" % cache_key)
			items = class_obj.all()
			items.filter(filter_key + ' = ', self)
			memcache.add(key=cache_key, value=[x for x in items])
		else:
			logging.debug("cache lookup for %s" % cache_key)

		return items

	def flush_cached_items(self, class_obj):
		cache_key = str(self) + '_' + class_obj.__name__
		memcache.delete(cache_key)

class Plan(PlansterModel):
	title = db.StringProperty()
	created = db.DateTimeProperty(auto_now_add=True)
	instructions = db.TextProperty()

	def get_options(self):
		return self.get_cached_items(Option, 'plan')

	def add_option(self, name):
		option = Option(plan=self, name=name)
		option.put()
		self.flush_cached_items(Option)
		return option

	def get_participants(self):
		return self.get_cached_items(Participant, 'plan')

	def add_participant(self, name):
		participant = Participant(plan=self, name=name)
		participant.put()
		self.flush_cached_items(Participant)

	def exists(self):
		return self.get_by_key_name(str(self.key().name))

	def __str__(self):
		return self.key().name()

	options = property(fget=lambda self: self.get_options())
	participants = property(fget=lambda self: self.get_participants())

class Participant(PlansterModel):
	name = db.StringProperty()
	plan = db.ReferenceProperty(Plan)

	def get_responses(self):
		return self.get_cached_items(Response, 'participant')

	def get_response(self, option):
		responses = self.get_responses()
		for response in responses:
			if response.option.key() == option.key():
				return response

	def delete(self):
		responses = self.get_responses()
		for response in responses:
			response.delete()
		self.plan.flush_cached_items(Participant)
		super(Participant, self).delete()

	def put(self):
		self.plan.flush_cached_items(Participant)
		super(Participant, self).put()

	def __str__(self):
		return str(self.key())

class Option(PlansterModel):
	name = db.StringProperty()
	plan = db.ReferenceProperty(Plan)

	def get_responses(self):
		return self.get_cached_items(Response, 'option')

	def delete(self):
		responses = self.get_responses()
		for response in responses:
			response.delete()
		self.plan.flush_cached_items(Option)
		super(Option, self).delete()

class Response(db.Model):
	option = db.ReferenceProperty(Option)
	participant = db.ReferenceProperty(Participant)
	value = db.IntegerProperty()

	def delete(self):
		self.participant.flush_cached_items(Response)
		super(Response, self).delete()

	def put(self):
		self.participant.flush_cached_items(Response)
		super(Response, self).put()
