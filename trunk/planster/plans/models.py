#
# Copyright (C) 2009 Stefan Ott, all rights reserved
#
from django.db import models
from django.contrib.auth.models import User
from random import seed, choice
import string
from django.db.models.signals import post_save

COUNT_TYPE_NONE = 1
COUNT_TYPE_ONLY_YES = 2
COUNT_TYPE_BOTH = 3

def create_profile(sender, instance, created, **kwargs):
	if created and isinstance(instance, User):
		profile = UserProfile(user=instance)
		profile.save()

post_save.connect(create_profile)

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
	title = models.CharField(max_length=50, default='Cunning plan')
	instructions = models.TextField(
		default='Please choose your preferred options')
	created = models.DateTimeField(auto_now_add=True)
	expires = models.DateTimeField()
	owner = models.EmailField()
	hash = models.CharField(max_length=15, unique=True,
		default=Planster.hash)
	count_type = models.IntegerField(choices=(
		(COUNT_TYPE_NONE, 'Hide totals'),
		(COUNT_TYPE_ONLY_YES, 'Count only "yes"'),
		(COUNT_TYPE_BOTH, 'Count "maybe" as half "yes"')
	), default=COUNT_TYPE_BOTH)

	def generate_hash(self):
		seed()
		chars = string.letters + string.digits
		return ''.join([choice(chars) for i in range(15)])

	def __get_people(self):
		return Participant.objects.filter(plan=self)

	def __get_options(self):
		return Option.objects.filter(plan=self)

	def __by_category(self):
		options = Option.objects.filter(plan=self)

		categories = {}

		for option in options:
			category = option.category
			if not category in categories:
				categories[category] = Category(category)

			categories[category].add(option)

		result = []
		for category in sorted(categories):
			result.append(categories[category])

		return result

	def __unicode__(self):
		return self.title;

	people = property(fget=__get_people)
	options = property(fget=__get_options)
	by_category = property(fget=__by_category)

class Participant(models.Model):
	name = models.CharField(max_length=100)
	plan = models.ForeignKey(Plan)

	def __unicode__(self):
		return self.name;

	def setResponse(self, option, value):
		response, created = Response.objects.get_or_create(
			participant=self, option=option,
			defaults={'value' : value})
		response.value = value
		response.save()

	def getResponse(self, option):
		try:
			response = Response.objects.get(
				participant=self, option=option)
			return response.value
		except:
			return 0

class Category(object):
	def __init__(self, name):
		self.name = name
		self.items = []

	def add(self, item):
		self.items.append(item)

class UserProfile(models.Model):
	user = models.ForeignKey(User, unique=True)
	plans = models.ManyToManyField("Plan")

class Option(models.Model):
	name = models.CharField(max_length=100)
	plan = models.ForeignKey(Plan)
	category = models.CharField(max_length=100, default='')

	def __unicode__(self):
		return self.name

	class Meta:
		ordering = ['category', 'name']

	def count(self):
		sum = 0

		responses = Response.objects.filter(option=self, value=1)
		sum += len(responses)

		if self.plan.count_type == COUNT_TYPE_BOTH:
			responses = Response.objects.filter(option=self,
					value=3)
			sum += len(responses)/2.0

		return sum

	def __get_tags(self):
		tags = Tag.objects.filter(option=self)
		result = []

		for tag in tags:
			result.append(tag.name)

		return result

	def __set_tags(self, tags):
		current_tags = Tag.objects.filter(option=self)

		for tag in current_tags:
			if tag.name not in tags:
				tag.delete()
			else:
				tags.remove(tag.name)

		for item in tags:
			tag = Tag(name=item.strip(), option=self)
			tag.save()

	tags = property(fget=__get_tags, fset=__set_tags)

class Tag(models.Model):
	name = models.CharField(max_length=100)
	option = models.ForeignKey(Option)

class Response(models.Model):
	option = models.ForeignKey(Option)
	participant = models.ForeignKey(Participant)
	value = models.IntegerField(choices=(
		(1, 'Yes'),
		(2, 'No'),
		(3, 'Maybe'),
		(4, 'No response'),
	))

	class Meta:
		unique_together = (('option', 'participant'),)

	def __unicode__(self):
		return "%s says %d to %s" % (
			self.participant, self.value, self.option)
