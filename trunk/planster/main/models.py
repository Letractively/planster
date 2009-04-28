#
# Copyright (C) 2009 Stefan Ott, all rights reserved
#
from django.db import models
from random import seed, choice
import string

class Callable:
	def __init__(self, anycallable):
		self.__call__ = anycallable

class Planster(object):
	def generate_hash():
		seed()
		chars = string.letters + string.digits
		return ''.join([choice(chars) for i in range(15)])

	hash = Callable(generate_hash)

class Plan(models.Model):
	title = models.CharField(max_length=50, default='Unnamed PLAN')
	instructions = models.TextField(
		default='Please choose your preferred options')
	created = models.DateTimeField(auto_now_add=True)
	expires = models.DateTimeField()
	owner = models.EmailField()
	participants_locked = models.BooleanField(default=False)
	settings_locked = models.BooleanField(default=False)
	hash = models.CharField(max_length=15, unique=True,
		default=Planster.hash)

	def generate_hash(self):
		seed()
		chars = string.letters + string.digits
		return ''.join([choice(chars) for i in range(15)])

	def __get_people(self):
		return Participant.objects.filter(plan=self)

	def __unicode__(self):
		return self.title;

	people = property(fget=__get_people)

class Participant(models.Model):
	name = models.CharField(max_length=100)
	plan = models.ForeignKey(Plan)

	def __unicode__(self):
		return self.name;

class Option(models.Model):
	name = models.CharField(max_length=100)
	plan = models.ForeignKey(Plan)

	def __unicode__(self):
		return self.name

class Response(models.Model):
	option = models.ForeignKey(Option)
	participant = models.ForeignKey(Participant)
	value = models.IntegerField(choices=(
		(1, 'Yes'),
		(2, 'No'),
		(3, 'Maybe'),
		(4, 'No response'),
	))

	def __unicode__(self):
		return "%s says %d to %s" % (
			self.participant, self.value, self.option)
