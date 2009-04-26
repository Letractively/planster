# coding=utf8
import unittest
import urllib
import httplib
from google.appengine.api import users
from django.utils import simplejson as json


class TestAPI(unittest.TestCase):
	cookie = None

	def setUp(self):
		self.__createPlan()

	def __login(self, as='test@example.com'):
		data = urllib.urlencode({
			'email': as,
			'admin': False,
			'action': 'Login'
		})
		answer = self.__http_post_root('/_ah/login', data)
		self.cookie = answer.getheader('set-cookie')

	def __logout(self):
		data = urllib.urlencode({
			'admin': False,
			'action': 'Logout'
		})
		answer = self.__http_post_root('/_ah/login', data)
		self.cookie = answer.getheader('set-cookie')

	def __http_post_root(self, url, data):
		connection = httplib.HTTPConnection('localhost', '8080')
		headers = {"Content-type": "application/x-www-form-urlencoded",
			"Accept": "application/json"}
		if self.cookie:
			headers['Cookie'] = self.cookie
		connection.request("POST", url, data, headers)
		response = connection.getresponse()
		return response

	def __http_post(self, url, data):
		connection = httplib.HTTPConnection('localhost', '8080')
		headers = {"Content-type": "application/x-www-form-urlencoded",
			"Accept": "application/json"}
		if self.cookie:
			headers['Cookie'] = self.cookie
		connection.request("POST", '/rpc/' + url, data, headers)
		response = connection.getresponse()
		return response

	def __http_post_put(self, url, data):
		params = urllib.urlencode({
			'_method': 'put',
			'data': data
		})
		connection = httplib.HTTPConnection('localhost', '8080')
		headers = {"Content-type": "application/x-www-form-urlencoded",
			"Accept": "application/json"}
		if self.cookie:
			headers['Cookie'] = self.cookie
		connection.request("POST", '/rpc/' + url, params, headers)
		response = connection.getresponse()
		return response

	def __http_put(self, url, data):
		connection = httplib.HTTPConnection('localhost', '8080')
		headers = {"Content-type": "application/x-www-form-urlencoded",
			"Accept": "application/json"}
		if self.cookie:
			headers['Cookie'] = self.cookie
		connection.request("PUT", '/rpc/' + url, data, headers)
		response = connection.getresponse()
		return response

	def __http_get(self, url):
		connection = httplib.HTTPConnection('localhost', '8080')
		headers = {}
		if self.cookie:
			headers['Cookie'] = self.cookie
		connection.request("GET", '/rpc/' + url, '', headers)
		response = connection.getresponse()
		return response

	def __createPlan(self):
		answer = self.__http_put('', json.dumps({}))
		data = json.loads(answer.read())
		self.planID = data['id']
		self.planKey = data['key']

	def testCreatePlan(self):
		data = json.dumps({})
		answer = self.__http_put('', data)
		data = json.loads(answer.read())
		self.assertTrue(len(data['id']) > 0)
		self.assertTrue(len(data['key']) > 0)
		self.assertEquals('Unnamed PLAN', data['title'])
		self.assertEqual(201, answer.status)
		self.assertEqual('/rpc/' + data['id'],
			answer.getheader('Location'))

		data = json.dumps({'title': 'Test PLAN'})
		answer = self.__http_put('', data)
		data = json.loads(answer.read())
		self.assertTrue(len(data['id']) > 0)
		self.assertTrue(len(data['key']) > 0)
		self.assertEquals('Test PLAN', data['title'])

	def testGetTitle(self):
		answer = self.__http_get(self.planID + '/title')
		title = answer.read()
		self.assertEqual('Unnamed PLAN', title)

	def testSetTitle(self):
		data = urllib.urlencode({'title': 'Some new title'})
		answer = self.__http_post(self.planID + '/title', data)
		self.assertEqual(200, answer.status)
		self.assertEqual('Some new title', answer.read())

		answer = self.__http_get(self.planID + '/title')
		self.assertEqual(200, answer.status)
		self.assertEqual('Some new title', answer.read())

		data = urllib.urlencode({'title': 'Some new<a href=""> title'})
		answer = self.__http_post(self.planID + '/title', data)
		self.assertEqual(200, answer.status)
		title = answer.read()
		self.assertEqual('Some new&lt;a href=""&gt; title', title)

	def testUnicodeTitle(self):
		title = u'fünf'
		data = urllib.urlencode({'title': title.encode('utf8')})
		answer = self.__http_post(self.planID + '/title', data)
		self.assertEqual(title, answer.read().decode('utf8'))

	def testGetInstructions(self):
		answer = self.__http_get(self.planID + '/instructions')
		instructions = answer.read()
		self.assertEquals("", instructions)

	def testSetInstructions(self):
		data = 'instructions=Do <b>bar\n\nand foo'
		answer = self.__http_post(self.planID + '/instructions', data)
		self.assertEquals('Do &lt;b&gt;bar<br />\n<br />\nand foo',
			answer.read())

		answer = self.__http_get(self.planID + '/instructions')
		self.assertEquals('Do &lt;b&gt;bar<br />\n<br />\nand foo',
			answer.read())

	def testUnicodeInstructions(self):
		instructions = u'fünf instructions are enough'
		data = urllib.urlencode({'instructions': instructions.encode(
			'utf8')})
		answer = self.__http_post(self.planID + '/instructions', data)
		self.assertEqual(instructions, answer.read().decode('utf8'))

	def testPutItem(self):
		" TODO: this should be /items "

		data = json.dumps({'title': 'Pizza'})
		answer = self.__http_put(self.planID + '/options', data)
		data = answer.read()
		new_item = json.loads(data)
		new_id = new_item['id']

		self.assertEqual(201, answer.status)
		self.assertTrue(len(new_id) > 0)
		self.assertEqual('Pizza', new_item['title'])
		self.assertEqual('/rpc/' + self.planID + '/options/' + new_id,
			answer.getheader('Location'))

	def testPutUnicodeItem(self):
		title = u'fünf'
		data = json.dumps({'title':  title})
		answer = self.__http_put(self.planID + '/options', data)
		data = answer.read()
		new_item = json.loads(data)
		self.assertEqual(title, new_item['title'])

	def testPutItemViaPost(self):
		data = json.dumps({'title': 'Orange juice'})
		answer = self.__http_post_put(self.planID + '/options', data)
		data = answer.read()
		new_item = json.loads(data)
		new_id = new_item['id']

		self.assertEqual(201, answer.status)
		self.assertTrue(len(new_id) > 0)
		self.assertEqual('Orange juice', new_item['title'])
		self.assertEqual('/rpc/' + self.planID + '/options/' + new_id,
			answer.getheader('Location'))

		data = json.dumps({'title': u'fünf'})
		answer = self.__http_post_put(self.planID + '/options', data)
		data = answer.read()
		new_item = json.loads(data)
		new_id = new_item['id']
		new_title = new_item['title']

		self.assertEqual(201, answer.status)
		self.assertTrue(len(new_id) > 0)
		self.assertEqual(u'fünf', new_title)

	def testPutItems(self):
		items = ['Pizza', 'Pasta', 'Meat']

		for item in items:
			data = json.dumps({'title': item})
			self.__http_put(self.planID + '/options', data)

		answer = self.__http_get(self.planID + '/options')
		data = json.loads(answer.read())

		self.assertEqual(3, len(data))

	def testPutItemsViaPost(self):
		items = ['Pizza', 'Pasta']

		for item in items:
			data = json.dumps({'title': item})
			self.__http_post_put(self.planID + '/options', data)

		answer = self.__http_get(self.planID + '/options')
		data = json.loads(answer.read())

		self.assertEqual(2, len(data))

	def testSetOwner(self):
		answer = self.__http_put(self.planID + '/owner', '')
		self.assertEqual(answer.status, 401) # not authorized

		self.__login(as = 'testrunner@example.org')
		answer = self.__http_put(self.planID + '/owner', '')
		self.assertEqual(answer.status, 400) # illegal request

		data = json.dumps({})
		answer = self.__http_put(self.planID + '/owner', data)
		self.assertEqual(answer.status, 400) # illegal request

		data = json.dumps({'key': 'wrong key'})
		answer = self.__http_put(self.planID + '/owner', data)
		self.assertEqual(answer.status, 403) # forbidden

		data = json.dumps({'key': self.planKey})
		answer = self.__http_put(self.planID + '/owner', data)
		self.assertEqual(answer.status, 200) # OK
		result = json.loads(answer.read())
		self.assertEquals('testrunner@example.org', result['owner']) 

		answer = self.__http_put(self.planID + '/owner', data)
		self.assertEqual(answer.status, 409) # exists

	def testSetOwnerViaPost(self):
		self.__login(as = 'testrunner@example.org')
		data = json.dumps({'key': self.planKey})
		answer = self.__http_post_put(self.planID + '/owner', data)
		data = json.loads(answer.read())

		self.assertEquals(answer.status, 200)
		self.assertEquals('testrunner@example.org', data['owner']) 

	def testGetOwner(self):
		self.__login(as = 'testrunner@example.org')
		data = json.dumps({'key': self.planKey})
		answer = self.__http_put(self.planID + '/owner', data)
		self.assertEqual(answer.status, 200, "PUT failed") # OK

		answer = self.__http_get(self.planID + '/owner')
		result = json.loads(answer.read())
		self.assertEqual(answer.status, 200) # OK
		self.assertEqual('testrunner@example.org', result['owner'])

		self.__logout()
		answer = self.__http_get(self.planID + '/owner')
		self.assertEqual(answer.status, 401) # not authorized
		self.assertEqual('', answer.read())

	def testSetGetPermissions(self):
		" TODO "
		pass

if __name__ == '__main__':
    unittest.main()

