#!/usr/bin/env python
#
# Copyright 2004-2009 Stefan Ott
#
# Licensed under the GNU GPL v2.
#

import logging
from google.appengine.ext import db
from google.appengine.api import users
from google.appengine.ext import webapp
from google.appengine.api import memcache
from google.appengine.ext.webapp import template

class PermissionDeniedException(Exception):
	pass

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
	__title = db.StringProperty(name='title')
	__instructions = db.TextProperty(name='instructions')
	created = db.DateTimeProperty(auto_now_add=True)
	owner = db.UserProperty()
	participants_locked = db.BooleanProperty()
	settings_locked = db.BooleanProperty()

	def add_option(self, name):
		option = Option(plan=self, name=name)
		option.put()
		self.flush_cached_items(Option)
		return option

	def add_participant(self, name):
		if not self.accepts_participants_from_current_user():
			raise PermissionDeniedException()

		participant = Participant(plan=self, name=name)
		participant.put()
		self.flush_cached_items(Participant)

	def exists(self):
		return self.get_by_key_name(str(self.key().name))

	def accepts_participants_from_current_user(self):
		if not self.participants_locked:
			return True
		return users.get_current_user() == self.owner

	def can_be_edited_by_current_user(self):
		if not self.settings_locked:
			return True
		return users.get_current_user() == self.owner

	def __str__(self):
		return self.key().name()

	" accessors for properties "

	def __get_options(self):
		return self.get_cached_items(Option, 'plan')

	def __get_participants(self):
		return self.get_cached_items(Participant, 'plan')

	def __get_instructions(self):
		return self.__instructions

	def __set_instructions(instructions):
		if not self.can_be_edited_by_current_user():
			raise PermissionDeniedException()
		self.__instructions = instructions

	def __get_title(self):
		return self.__title

	def __set_title(self, title):
		if not self.can_be_edited_by_current_user():
			raise PermissionDeniedException()
		self.__title = title

	options = property(__get_options)
	participants = property(__get_participants)
	title = property(__get_title, __set_title)
	instructions = property(__get_instructions, __set_instructions)

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
