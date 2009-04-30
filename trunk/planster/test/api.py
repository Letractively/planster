# coding=utf8
import unittest
import urllib
import httplib
#from google.appengine.api import users
from django.utils import simplejson as json

port=8000

class TestAPI(unittest.TestCase):
	cookie = None

	def setUp(self):
		self.__createPlan()

	def __login(self, id='test@example.com'):
		data = urllib.urlencode({
			'email': id,
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
		connection = httplib.HTTPConnection('localhost', port)
		headers = {"Content-type": "application/x-www-form-urlencoded",
			"Accept": "application/json"}
		if self.cookie:
			headers['Cookie'] = self.cookie
		connection.request("POST", url, data, headers)
		response = connection.getresponse()
		return response

	def __http_post(self, url, data):
		connection = httplib.HTTPConnection('localhost', port)
		headers = {"Content-type": "application/x-www-form-urlencoded",
			"Accept": "application/json"}
		if self.cookie:
			headers['Cookie'] = self.cookie
		connection.request("POST", '/rpc/' + url, json.dumps(data),
				headers)
		response = connection.getresponse()
		return response

	def __http_post_put(self, url, data):
		params = urllib.urlencode({
			'_method': 'put',
			'data': json.dumps(data)
		})
		connection = httplib.HTTPConnection('localhost', port)
		headers = {"Content-type": "application/x-www-form-urlencoded",
			"Accept": "application/json"}
		if self.cookie:
			headers['Cookie'] = self.cookie
		connection.request("POST", '/rpc/' + url, params, headers)
		response = connection.getresponse()
		return response

	def __http_put(self, url, data):
		connection = httplib.HTTPConnection('localhost', port)
		headers = {"Content-type": "application/x-www-form-urlencoded",
			"Accept": "application/json"}
		if self.cookie:
			headers['Cookie'] = self.cookie
		connection.request("PUT", '/rpc/' + url, json.dumps(data),
				headers)
		response = connection.getresponse()
		return response

	def __http_get(self, url):
		connection = httplib.HTTPConnection('localhost', port)
		headers = {}
		if self.cookie:
			headers['Cookie'] = self.cookie
		connection.request("GET", '/rpc/' + url, '', headers)
		response = connection.getresponse()
		return response

	def __createPlan(self):
		self.plan = {'title': 'a test plan', 'instructions': 'run'}
		answer = self.__http_put('', self.plan)
		if answer.status != 201:
			raise "Unable to create plan, code %d" % answer.status

		data = json.loads(answer.read())
		self.planID = data['id']

	def testCreatePlan(self):
		data = {}
		answer = self.__http_put('', data)
		self.assertEqual(201, answer.status)

		data = json.loads(answer.read())
		self.assertTrue('id' in data)
		self.assertTrue('title' in data)
		self.assertTrue('instructions' in data)

		self.assertTrue(len(data['id']) > 0)
		self.assertEquals('Unnamed PLAN', data['title'])
		self.assertTrue(answer.getheader('Location').endswith(
			'/rpc/' + data['id']))

		plan = {'title': 'Test PLAN', 'instructions': 'do this!'}
		answer = self.__http_put('', plan)
		data = json.loads(answer.read())
		self.assertTrue(len(data['id']) > 0)
		self.assertEquals(plan['title'], data['title'])
		self.assertEquals(plan['instructions'], data['instructions'])

	def testGetPlan(self):
		answer = self.__http_get(self.planID)
		self.assertEqual(200, answer.status)
		data = json.loads(answer.read())

		self.assertTrue('id' in data)
		self.assertTrue('title' in data)
		self.assertTrue('instructions' in data)

		self.assertTrue(len(data['id']) > 0)
		self.assertEquals(self.plan['title'], data['title'])
		self.assertEquals(self.plan['instructions'],
				data['instructions'])

	def testEditTitle(self):
		mydata = {'title': 'some new title, totally different'}
		answer = self.__http_post(self.planID, mydata)
		self.assertEqual(200, answer.status)
		data = json.loads(answer.read())

		self.assertTrue('title' in data)
		self.assertEquals(mydata['title'], data['title'])

		answer = self.__http_get(self.planID)
		data = json.loads(answer.read())
		self.assertEquals(mydata['title'], data['title'])

	def testTitleSpecialCharacters(self):
		mydata = {'title': 'Some new<a href=""> t'}
		answer = self.__http_post(self.planID, mydata)
		self.assertEqual(200, answer.status)
		data = json.loads(answer.read())

		self.assertEquals('Some new&lt;a href=""&gt; t', data['title'])

	def testUnicodeTitle(self):
		mydata = {'title': u'fünf'}
		answer = self.__http_post(self.planID, mydata)
		self.assertEqual(200, answer.status)
		data = json.loads(answer.read())

		self.assertTrue('title' in data)
		self.assertEquals(mydata['title'], data['title'])

		answer = self.__http_get(self.planID)
		data = json.loads(answer.read())
		self.assertEquals(mydata['title'], data['title'])

	def testEditInstructions(self):
		mydata = {'instructions': 'yada yada'}
		answer = self.__http_post(self.planID, mydata)
		self.assertEqual(200, answer.status)
		data = json.loads(answer.read())

		self.assertTrue('instructions' in data)
		self.assertEquals(mydata['instructions'], data['instructions'])

		answer = self.__http_get(self.planID)
		data = json.loads(answer.read())
		self.assertEquals(mydata['instructions'], data['instructions'])

	def testInstructionsSpecialCharacters(self):
		mydata = {'instructions': 'Do <b>bar\n\nand foo' }
		answer = self.__http_post(self.planID, mydata)
		self.assertEqual(200, answer.status)
		data = json.loads(answer.read())

		self.assertEquals(data['instructions'],
			'Do &lt;b&gt;bar<br />\n<br />\nand foo')

	def testUnicodeInstructinos(self):
		mydata = {'instructions': u'fünf instructions are enough'}
		answer = self.__http_post(self.planID, mydata)
		self.assertEqual(200, answer.status)
		data = json.loads(answer.read())

		self.assertEquals(mydata['instructions'], data['instructions'])

	def testPutOption(self):
		mydata = {'title': 'Pizza'}
		answer = self.__http_put(self.planID + '/options', mydata)
		self.assertEqual(201, answer.status)
		data = json.loads(answer.read())

		self.assertTrue(data['id'] > 0)
		self.assertEqual(mydata['title'], data['title'])
		self.assertTrue(answer.getheader('Location').endswith(
			'/rpc/' + self.planID + '/options/' + data['id']))

	def testPutUnicodeItem(self):
		mydata = {'title': u'fünf'}
		answer = self.__http_put(self.planID + '/options', mydata)
		data = json.loads(answer.read())
		self.assertEqual(mydata['title'], data['title'])

	def testPutItemViaPost(self):
		data = {'title': 'Orange juice'}
		answer = self.__http_post_put(self.planID + '/options', data)
		data = answer.read()
		new_item = json.loads(data)
		new_id = new_item['id']

		self.assertEqual(201, answer.status)
		self.assertTrue(len(new_id) > 0)
		self.assertEqual('Orange juice', new_item['title'])
		self.assertTrue(answer.getheader('Location').endswith(
			'/rpc/' + self.planID + '/options/' + new_id))

		data = {'title': u'fünf'}
		answer = self.__http_post_put(self.planID + '/options', data)
		data = answer.read()
		new_item = json.loads(data)
		new_id = new_item['id']
		new_title = new_item['title']

		self.assertEqual(201, answer.status)
		self.assertTrue(len(new_id) > 0)
		self.assertEqual(u'fünf', new_title)

	def testModifyItem(self):
		bad = {'title': 'Something bad'}
		good = {'title': 'Something good'}

		answer = self.__http_put(self.planID + '/options', bad)
		self.assertEqual(201, answer.status)
		data = json.loads(answer.read())
		self.assertTrue('title' in data)
		self.assertTrue('id' in data)
		self.assertEqual(bad['title'], data['title'])
		self.assertTrue(data['id'] > 0)

		id = str(data['id'])

		answer = self.__http_get(self.planID + '/options/' + id)
		self.assertTrue('title' in data)
		self.assertEqual(bad['title'], data['title'])

		answer = self.__http_post(self.planID + '/options/' + id, good)
		self.assertEqual(200, answer.status)
		data = json.loads(answer.read())
		self.assertTrue('title' in data)
		self.assertEqual(good['title'], data['title'])

		answer = self.__http_get(self.planID + '/options/' + id)
		self.assertTrue('title' in data)
		self.assertEqual(good['title'], data['title'])


	def testPutItems(self):
		items = ['Pizza', 'Pasta', 'Meat']

		for item in items:
			data = {'title': item}
			self.__http_put(self.planID + '/options', data)

		answer = self.__http_get(self.planID + '/options')
		data = json.loads(answer.read())

		self.assertEqual(3, len(data))

	def testPutItemsViaPost(self):
		items = ['Pizza', 'Pasta']

		for item in items:
			data = {'title': item}
			self.__http_post_put(self.planID + '/options', data)

		answer = self.__http_get(self.planID + '/options')
		data = json.loads(answer.read())

		self.assertEqual(2, len(data))

	def testAddPerson(self):
		mydata = {'name': 'Peter P. Rat'}
		answer = self.__http_put(self.planID + '/people', mydata)
		self.assertEqual(answer.status, 201) # Created

		peter = json.loads(answer.read())
		self.assertTrue(peter['id'] > 0)
		self.assertEqual(mydata['name'], peter['name'])
		self.assertTrue(answer.getheader('Location').endswith(
			'/rpc/' + self.planID + '/people/' + str(peter['id'])))

		frank = {'name': 'Frank'}
		answer = self.__http_put(self.planID + '/people', frank)
		frank = json.loads(answer.read())

		answer = self.__http_get(self.planID + '/people')
		data = json.loads(answer.read())
		self.assertEqual(2, len(data))
		self.assertTrue(peter in data)
		self.assertTrue(frank in data)

	def testModifyPerson(self):
		frank = {'name': 'Frank'}
		answer = self.__http_put(self.planID + '/people', frank)
		self.assertEqual(answer.status, 201) # Created
		data = json.loads(answer.read())
		self.assertEqual(frank['name'], data['name'])
		self.assertTrue(data['id'] > 0)
		id = str(data['id'])

		answer = self.__http_get(self.planID + '/people/' + id)
		data = json.loads(answer.read())
		self.assertTrue('name' in data)
		self.assertEqual(frank['name'], data['name'])

		joe = {'name': 'Joe'}
		answer = self.__http_post(self.planID + '/people/' +
				str(data['id']), joe)
		self.assertEqual(answer.status, 200)
		data = json.loads(answer.read())
		self.assertEqual(joe['name'], data['name'])

		answer = self.__http_get(self.planID + '/people/' + id)
		data = json.loads(answer.read())
		self.assertTrue('name' in data)
		self.assertEqual(joe['name'], data['name'])

	def testSetResponse(self):
		jack = {'name': 'Jack'}
		john = {'name': 'John'}

		answer = self.__http_put(self.planID + '/people', jack)
		jack['id'] = json.loads(answer.read())['id']

		answer = self.__http_put(self.planID + '/people', john)
		john['id'] = json.loads(answer.read())['id']

		meat = {'title': 'meat'}
		fish = {'title': 'fish'}

		answer = self.__http_post_put(self.planID + '/options', meat)
		meat['id'] = json.loads(answer.read())['id']

		answer = self.__http_post_put(self.planID + '/options', fish)
		fish['id'] = json.loads(answer.read())['id']

		answer = self.__http_post('%s/people/%s/responses' % (
			self.planID, str(jack['id'])), {
				meat['id']: 1,
				fish['id']: 3
			})
		self.assertEquals(answer.status, 200)

		result = answer.read()
		data = json.loads(result)
		self.assertEqual(2, len(data))
		self.assertTrue(meat['id'] in data)
		self.assertTrue(fish['id'] in data)
		self.assertEqual(1, data[meat['id']])
		self.assertEqual(3, data[fish['id']])

"""	def testSetOwner(self):
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

		try:
			data = json.loads(answer.read())
		except:
			raise Exception("Failed to parse JSON")

		self.assertEquals(answer.status, 200)
		self.assertEquals('testrunner@example.org', data['owner'])

	def testGetOwner(self):
		self.__login(as = 'testrunner@example.org')
		data = json.dumps({'key': self.planKey})
		answer = self.__http_put(self.planID + '/owner', data)
		self.assertEqual(answer.status, 200, "PUT failed") # OK

		answer = self.__http_get(self.planID + '/owner')
		try:
			result = json.loads(answer.read())
		except:
			raise Exception("Failed to parse JSON")

		self.assertEqual(answer.status, 200) # OK
		self.assertEqual('testrunner@example.org', result['owner'])

		self.__logout()
		answer = self.__http_get(self.planID + '/owner')
		self.assertEqual(answer.status, 401) # not authorized
		self.assertEqual('', answer.read())

	def testSetGetPermissions(self):
		" TODO "
		pass"""

if __name__ == '__main__':
	unittest.main()

