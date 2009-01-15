# coding=utf8
import unittest
import urllib
import urllib2
import httplib
from django.utils import simplejson as json

class TestAPI(unittest.TestCase):
	def setUp(self):
		self.__createPlan()

	def __http_post2(self, url, data):
		" TODO: delete this one, once the API has been fixed "
		opener = urllib2.build_opener(urllib2.HTTPHandler)
		request = urllib2.Request(url, data = data)
		request.get_method = lambda: 'POST'
		request.add_header("Content-type",
			"application/x-www-form-urlencoded")
		url = opener.open(request)
		return url.read()

	def __http_post(self, url, data):
		connection = httplib.HTTPConnection('localhost', '8080')
		headers = {"Content-type": "application/x-www-form-urlencoded"}
		connection.request("POST", '/rpc/' + url, data, headers)
		response = connection.getresponse()
		return response

	def __http_post_put(self, url, data):
		params = urllib.urlencode({
			'_method': 'put',
			'data': data
		})
		connection = httplib.HTTPConnection('localhost', '8080')
		headers = {"Content-type": "application/x-www-form-urlencoded"}
		connection.request("POST", '/rpc/' + url, params, headers)
		response = connection.getresponse()
		return response

	def __http_put(self, url, data):
		connection = httplib.HTTPConnection('localhost', '8080')
		headers = {"Content-type": "application/x-www-form-urlencoded"}
		connection.request("PUT", '/rpc/' + url, data, headers)
		response = connection.getresponse()
		return response

	def __http_get(self, url):		
		connection = httplib.HTTPConnection('localhost', '8080')
		connection.request("GET", '/rpc/' + url)
		response = connection.getresponse()
		return response

	def __createPlan(self):
		answer = self.__http_post2('http://localhost:8080/create', '')	
		for line in answer.splitlines():
			if line.startswith('PLAN = '):
				self.planID = line[8:-2]

	def testGetTitle(self):
		answer = self.__http_get(self.planID + '/title')
		title = answer.read()
		self.assertEqual('Unnamed PLAN', title)

	def testSetTitle(self):
		" TODO: make the 'title=' part go away "
		data = urllib.urlencode({'title': 'Some new title'})
		answer = self.__http_post(self.planID + '/title', data)
		self.assertEqual('Some new title', answer.read())

		answer = self.__http_get(self.planID + '/title')
		self.assertEqual('Some new title', answer.read())

		title = 'Some new<a href=""> title'
		data = "title=" + title
		answer = self.__http_post(self.planID + '/title', data)
		self.assertEqual('Some new&lt;a href=""&gt; title', answer.read())

	def testUnicodeTitle(self):
		title = u'fünf'
		data = "title=" + title.encode('utf8')
		answer = self.__http_post(self.planID + '/title', data)
		self.assertEqual(title, answer.read().decode('utf8'))

	def testGetInstructions(self):
		answer = self.__http_get(self.planID + '/instructions')
		instructions = answer.read()
		self.assertEquals("", instructions)

	def testSetInstructions(self):
		" TODO: make the 'instructions=' part go away "
		" also, test unicode "
		data = 'instructions=Do <b>bar\n\nand foo'
		answer = self.__http_post(self.planID + '/instructions', data)
		self.assertEquals('Do &lt;b&gt;bar<br />\n<br />\nand foo',
			answer.read())

		answer = self.__http_get(self.planID + '/instructions')
		self.assertEquals('Do &lt;b&gt;bar<br />\n<br />\nand foo',
			answer.read())

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
		" TODO: include test for POST bypass "
		items = ['Pizza', 'Pasta', 'Meat']

		for item in items:
			data = json.dumps({'title': item})
			self.__http_put(self.planID + '/options', data)

		answer = self.__http_get(self.planID + '/options')
		data = json.loads(answer.read())

		self.assertEqual(3, len(data))

	"""
	def testSetGetOwner(self):
		" TODO "
		pass
	
	def testSetGetPermissions(self):
		" TODO "
		pass
	"""

if __name__ == '__main__':
    unittest.main()

