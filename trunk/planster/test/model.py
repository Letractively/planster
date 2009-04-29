# coding=utf8
#
# export PYTHONPATH=/home/stefan/projects/planster/trunk/
# export DJANGO_SETTINGS_MODULE="planster.settings"
import unittest
from planster.main.models import *
import datetime

port=8000

class TestModel(unittest.TestCase):
	def setUp(self):
		self.plan = Plan()
		self.plan.expires = datetime.date.today()
		self.plan.save()

		self.jack = Participant(plan=self.plan)
		self.jack.save()

		self.meat = Option(plan=self.plan)
		self.meat.save()

	def testSetResponse(self):
		self.assertEquals(len(Response.objects.filter(
			participant = self.jack)), 0)
		self.jack.setResponse(self.meat, 1)
		self.assertEquals(self.jack.getResponse(self.meat), 1)

		self.jack.setResponse(self.meat, 2)
		self.assertEquals(self.jack.getResponse(self.meat), 2)

		self.jack.setResponse(self.meat, 3)
		self.assertEquals(self.jack.getResponse(self.meat), 3)

		self.assertEquals(len(Response.objects.filter(
			participant = self.jack)), 1)

if __name__ == '__main__':
	unittest.main()

