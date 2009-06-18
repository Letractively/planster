# coding=utf8
import unittest
import urllib
import httplib
import datetime
#from google.appengine.api import users
from django.utils import simplejson as json

port=8000

HTTP_OK = 200
HTTP_CREATED = 201
HTTP_BADREQ = 400

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

	def __http_post_delete(self, url):
		params = urllib.urlencode({'_method': 'delete'})
		connection = httplib.HTTPConnection('localhost', port)
		headers = {"Accept": "application/json"}
		if self.cookie:
			headers['Cookie'] = self.cookie
		connection.request("POST", '/rpc/' + url, params, headers)
		response = connection.getresponse()
		return response

	def __http_delete(self, url):
		connection = httplib.HTTPConnection('localhost', port)
		headers = {"Accept": "application/json"}
		if self.cookie:
			headers['Cookie'] = self.cookie
		connection.request("DELETE", '/rpc/' + url, '', headers)
		response = connection.getresponse()
		return response

	def __http_get(self, url):
		connection = httplib.HTTPConnection('localhost', port)
		headers = {"Accept": "application/json"}
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
		plan = {}
		answer = self.__http_put('', plan)
		self.assertEqual(201, answer.status)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))

		data = json.loads(answer.read())
		self.assertTrue('id' in data)
		self.assertTrue('title' in data)
		self.assertTrue('instructions' in data)
		self.assertTrue('expires' in data)

		now = datetime.date.today()

		self.assertTrue(len(data['id']) > 0)
		self.assertEquals('Cunning plan', data['title'])
		self.assertTrue(answer.getheader('Location').endswith(
			'/rpc/' + data['id']))
		self.assertEquals('%d-%02d-%02d' % (now.year, now.month+1,
			now.day), data['expires'])

		plan = {'title': 'Test PLAN', 'instructions': 'do this!'}
		answer = self.__http_put('', plan)
		self.assertEqual(201, answer.status)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())
		self.assertTrue(len(data['id']) > 0)
		self.assertEquals(plan['title'], data['title'])
		self.assertEquals(plan['instructions'], data['instructions'])

	def testGetPlan(self):
		answer = self.__http_get(self.planID)
		self.assertEqual(200, answer.status)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())

		self.assertTrue('id' in data)
		self.assertTrue('title' in data)
		self.assertTrue('instructions' in data)

		self.assertTrue(len(data['id']) > 0)
		self.assertEquals(self.plan['title'], data['title'])
		self.assertEquals(self.plan['instructions'],
				data['instructions'])

	def testExpires(self):
		answer = self.__http_put('', {'expires': 1 })
		self.assertEqual(201, answer.status)
		data = json.loads(answer.read())
		self.assertTrue('expires' in data)

		now = datetime.date.today()

		self.assertEquals('%d-%02d-%02d' % (now.year, now.month+1,
			now.day), data['expires'])

		answer = self.__http_put('', {'expires': -1 })
		self.assertEqual(201, answer.status)
		data = json.loads(answer.read())
		self.assertEquals('%d-%02d-%02d' % (now.year, now.month+1,
			now.day), data['expires'])

		answer = self.__http_put('', {'expires': 7 })
		self.assertEqual(201, answer.status)
		data = json.loads(answer.read())
		self.assertEquals('%d-%02d-%02d' % (now.year, now.month+6,
			now.day), data['expires'])

		answer = self.__http_put('', {'expires': '3' })
		self.assertEqual(201, answer.status)
		data = json.loads(answer.read())
		self.assertEquals('%d-%02d-%02d' % (now.year, now.month+3,
			now.day), data['expires'])

		answer = self.__http_put('', {'expires': 'invalid string' })
		self.assertEqual(400, answer.status)

	def testEditTitle(self):
		mydata = {'title': 'some new title, totally different'}
		answer = self.__http_post(self.planID, mydata)
		self.assertEqual(200, answer.status)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())

		self.assertTrue('title' in data)
		self.assertEquals(mydata['title'], data['title'])

		answer = self.__http_get(self.planID)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())
		self.assertEquals(mydata['title'], data['title'])

	def testEditCountType(self):
		mydata = {'count_type': 1}
		answer = self.__http_post(self.planID, mydata)
		self.assertEqual(200, answer.status)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())

		self.assertTrue('count_type' in data)
		self.assertEqual(1, data['count_type'])

		mydata['count_type'] = 2
		answer = self.__http_post(self.planID, mydata)
		data = json.loads(answer.read())
		self.assertEqual(2, data['count_type'])

		mydata['count_type'] = 4
		answer = self.__http_post(self.planID, mydata)
		data = json.loads(answer.read())
		self.assertEqual(2, data['count_type'])

	def testTitleSpecialCharacters(self):
		mydata = {'title': 'Some new<a href=""> t'}
		answer = self.__http_post(self.planID, mydata)
		self.assertEqual(200, answer.status)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())

		self.assertEquals('Some new&lt;a href=""&gt; t', data['title'])

	def testUnicodeTitle(self):
		mydata = {'title': u'fünf'}
		answer = self.__http_post(self.planID, mydata)
		self.assertEqual(200, answer.status)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())

		self.assertTrue('title' in data)
		self.assertEquals(mydata['title'], data['title'])

		answer = self.__http_get(self.planID)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())
		self.assertEquals(mydata['title'], data['title'])

	def testEditInstructions(self):
		mydata = {'instructions': 'yada yada'}
		answer = self.__http_post(self.planID, mydata)
		self.assertEqual(200, answer.status)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())

		self.assertTrue('instructions' in data)
		self.assertEquals(mydata['instructions'], data['instructions'])

		answer = self.__http_get(self.planID)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())
		self.assertEquals(mydata['instructions'], data['instructions'])

	def testInstructionsSpecialCharacters(self):
		mydata = {'instructions': 'Do <b>bar\n\nand foo' }
		answer = self.__http_post(self.planID, mydata)
		self.assertEqual(200, answer.status)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())

		self.assertEquals(data['instructions'],
			'Do &lt;b&gt;bar<br />\n<br />\nand foo')

	def testUnicodeInstructinos(self):
		mydata = {'instructions': u'fünf instructions are enough'}
		answer = self.__http_post(self.planID, mydata)
		self.assertEqual(200, answer.status)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())

		self.assertEquals(mydata['instructions'], data['instructions'])

	def testPutOption(self):
		mydata = {'title': 'Pizza'}
		answer = self.__http_put(self.planID + '/options', mydata)
		self.assertEqual(201, answer.status)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())

		self.assertTrue(data['id'] > 0)
		self.assertEqual(mydata['title'], data['title'])
		self.assertTrue(answer.getheader('Location').endswith(
			'/rpc/' + self.planID + '/options/' + data['id']))

	def testPutUnicodeItem(self):
		mydata = {'title': u'fünf'}
		answer = self.__http_put(self.planID + '/options', mydata)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())
		self.assertEqual(mydata['title'], data['title'])

	def testPutItemViaPost(self):
		data = {'title': 'Orange juice'}
		answer = self.__http_post_put(self.planID + '/options', data)
		self.assertEqual(201, answer.status)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))

		data = json.loads(answer.read())
		id = data['id']

		self.assertTrue(len(id) > 0)
		self.assertEqual('Orange juice', data['title'])
		self.assertTrue(answer.getheader('Location').endswith(
			'/rpc/' + self.planID + '/options/' + id))

		data = {'title': u'fünf'}
		answer = self.__http_post_put(self.planID + '/options', data)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())
		id = data['id']
		title = data['title']

		self.assertEqual(201, answer.status)
		self.assertTrue(len(id) > 0)
		self.assertEqual(u'fünf', title)

	def testModifyItem(self):
		bad = {'title': 'Something bad'}
		good = {'title': 'Something good'}

		answer = self.__http_put(self.planID + '/options', bad)
		self.assertEqual(201, answer.status)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))

		data = json.loads(answer.read())
		self.assertTrue('title' in data)
		self.assertTrue('id' in data)
		self.assertEqual(bad['title'], data['title'])
		self.assertTrue(data['id'] > 0)

		id = str(data['id'])

		answer = self.__http_get(self.planID + '/options/' + id)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		self.assertTrue('title' in data)
		self.assertEqual(bad['title'], data['title'])

		answer = self.__http_post(self.planID + '/options/' + id, good)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		self.assertEqual(200, answer.status)
		data = json.loads(answer.read())
		self.assertTrue('title' in data)
		self.assertEqual(good['title'], data['title'])

		answer = self.__http_get(self.planID + '/options/' + id)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		self.assertTrue('title' in data)
		self.assertEqual(good['title'], data['title'])

	def testDeleteItem(self):
		milk = {'title': 'Milk'}
		beer = {'title': 'Beer'}

		answer = self.__http_put(self.planID + '/options', milk)
		self.assertEqual(HTTP_CREATED, answer.status)

		answer = self.__http_put(self.planID + '/options', beer)
		self.assertEqual(HTTP_CREATED, answer.status)
		data = json.loads(answer.read())
		id = str(data['id'])

		answer = self.__http_get(self.planID + '/options')
		self.assertEqual(HTTP_OK, answer.status)
		data = json.loads(answer.read())
		self.assertEqual(len(data), 2)

		answer = self.__http_delete(self.planID + '/options/' + id)
		self.assertEqual(HTTP_OK, answer.status)

		answer = self.__http_get(self.planID + '/options')
		self.assertEqual(HTTP_OK, answer.status)
		data = json.loads(answer.read())
		self.assertEqual(len(data), 1)
		self.assertEqual(data[0]['title'], milk['title'])

		answer = self.__http_put(self.planID + '/options', beer)
		self.assertEqual(HTTP_CREATED, answer.status)
		data = json.loads(answer.read())
		id = str(data['id'])

		answer = self.__http_post_delete(self.planID + '/options/' + id)
		self.assertEqual(HTTP_OK, answer.status)

		answer = self.__http_get(self.planID + '/options')
		self.assertEqual(HTTP_OK, answer.status)
		data = json.loads(answer.read())
		self.assertEqual(len(data), 1)
		self.assertEqual(data[0]['title'], milk['title'])

	def testInvalidItem(self):
		data = {'title': ''}
		answer = self.__http_put(self.planID + '/options', data)
		self.assertEqual(answer.status, HTTP_BADREQ)

		data = {'title': '      '}
		answer = self.__http_put(self.planID + '/options', data)
		self.assertEqual(answer.status, HTTP_BADREQ)

		data = {'title': 'Milk'}
		answer = self.__http_put(self.planID + '/options', data)
		self.assertEqual(answer.status, HTTP_CREATED)
		milk = json.loads(answer.read())

		id = str(milk['id'])
		milk['title'] = '  '
		answer = self.__http_post(self.planID + '/options/' + id, milk)
		self.assertEqual(answer.status, HTTP_BADREQ)

		answer = self.__http_get(self.planID + '/options/' + id)
		self.assertEqual(answer.status, HTTP_OK)
		milk = json.loads(answer.read())
		self.assertEqual(milk['title'], data['title'])

	def testPutItems(self):
		items = ['Pizza', 'Pasta', 'Meat']

		for item in items:
			data = {'title': item}
			self.__http_put(self.planID + '/options', data)

		answer = self.__http_get(self.planID + '/options')
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())

		self.assertEqual(3, len(data))

	def testPutItemsViaPost(self):
		items = ['Pizza', 'Pasta']

		for item in items:
			data = {'title': item}
			self.__http_post_put(self.planID + '/options', data)

		answer = self.__http_get(self.planID + '/options')
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())

		self.assertEqual(2, len(data))

	def testAddPerson(self):
		mydata = {'name': 'Peter P. Rat'}
		answer = self.__http_put(self.planID + '/people', mydata)
		self.assertEqual(answer.status, 201) # Created
		self.assertEqual('application/json',
				answer.getheader('Content-type'))

		peter = json.loads(answer.read())
		self.assertTrue(peter['id'] > 0)
		self.assertEqual(mydata['name'], peter['name'])
		self.assertTrue(answer.getheader('Location').endswith(
			'/rpc/' + self.planID + '/people/' + str(peter['id'])))

		frank = {'name': 'Frank'}
		answer = self.__http_put(self.planID + '/people', frank)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		frank = json.loads(answer.read())

		answer = self.__http_get(self.planID + '/people')
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())
		self.assertEqual(2, len(data))
		self.assertTrue(peter in data)
		self.assertTrue(frank in data)

	def testInvalidPerson(self):
		data = {'name': ''}
		answer = self.__http_put(self.planID + '/people', data)
		self.assertEqual(answer.status, HTTP_BADREQ)

		data = {'name': '      '}
		answer = self.__http_put(self.planID + '/people', data)
		self.assertEqual(answer.status, HTTP_BADREQ)

		data = {'name': 'John'}
		answer = self.__http_put(self.planID + '/people', data)
		self.assertEqual(answer.status, HTTP_CREATED)
		john = json.loads(answer.read())

		id = str(john['id'])
		john['name'] = '  '
		answer = self.__http_post(self.planID + '/people/' + id, john)
		self.assertEqual(answer.status, HTTP_BADREQ)

		answer = self.__http_get(self.planID + '/people/' + id)
		self.assertEqual(answer.status, HTTP_OK)
		john = json.loads(answer.read())
		self.assertEqual(john['name'], data['name'])

	def testModifyPerson(self):
		frank = {'name': 'Frank'}
		answer = self.__http_put(self.planID + '/people', frank)
		self.assertEqual(answer.status, 201) # Created
		self.assertEqual('application/json',
				answer.getheader('Content-type'))

		data = json.loads(answer.read())
		self.assertEqual(frank['name'], data['name'])
		self.assertTrue(data['id'] > 0)
		id = str(data['id'])

		answer = self.__http_get(self.planID + '/people/' + id)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())
		self.assertTrue('name' in data)
		self.assertEqual(frank['name'], data['name'])

		joe = {'name': 'Joe'}
		answer = self.__http_post(self.planID + '/people/' +
				str(data['id']), joe)
		self.assertEqual(answer.status, 200)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		data = json.loads(answer.read())
		self.assertEqual(joe['name'], data['name'])

		answer = self.__http_get(self.planID + '/people/' + id)
		data = json.loads(answer.read())
		self.assertTrue('name' in data)
		self.assertEqual(joe['name'], data['name'])

	def testDeletePerson(self):
		john = {'name': 'John Doe'}
		jack = {'name': 'Jack Doe'}
		answer = self.__http_put(self.planID + '/people', john)
		self.assertEqual(answer.status, 201)

		answer = self.__http_put(self.planID + '/people', jack)
		self.assertEqual(answer.status, 201)
		data = json.loads(answer.read())
		id = data['id']

		answer = self.__http_get(self.planID + '/people')
		data = json.loads(answer.read())
		self.assertEqual(2, len(data))

		answer = self.__http_delete(self.planID + '/people/' + str(id))
		self.assertEqual(answer.status, 200)

		answer = self.__http_get(self.planID + '/people')
		data = json.loads(answer.read())
		self.assertEqual(1, len(data))
		self.assertEqual(john['name'], data[0]['name'])

		answer = self.__http_put(self.planID + '/people', jack)
		self.assertEqual(answer.status, 201)
		data = json.loads(answer.read())
		id = data['id']

		answer = self.__http_get(self.planID + '/people')
		data = json.loads(answer.read())
		self.assertEqual(2, len(data))

		answer = self.__http_post_delete(self.planID + '/people/' +
				str(id))
		self.assertEqual(answer.status, 200)

		answer = self.__http_get(self.planID + '/people')
		data = json.loads(answer.read())
		self.assertEqual(1, len(data))
		self.assertEqual(john['name'], data[0]['name'])

	def testSetResponse(self):
		jack = {'name': 'Jack'}
		john = {'name': 'John'}

		answer = self.__http_put(self.planID + '/people', jack)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		jack['id'] = json.loads(answer.read())['id']

		answer = self.__http_put(self.planID + '/people', john)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		john['id'] = json.loads(answer.read())['id']

		meat = {'title': 'meat'}
		fish = {'title': 'fish'}

		answer = self.__http_post_put(self.planID + '/options', meat)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		meat['id'] = json.loads(answer.read())['id']

		answer = self.__http_post_put(self.planID + '/options', fish)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))
		fish['id'] = json.loads(answer.read())['id']

		answer = self.__http_post('%s/people/%s/responses' % (
			self.planID, str(jack['id'])), {
				meat['id']: 1,
				fish['id']: 3
			})
		self.assertEquals(answer.status, 200)
		self.assertEqual('application/json',
				answer.getheader('Content-type'))

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

