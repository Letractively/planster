from django import template

register = template.Library()

@register.filter
def response_text(value):
	text = [ '-', 'yes', 'no', 'maybe' ]

	if value:
		return text[value]
	else:
		return text[0]

@register.filter
def response_img(value):
	images = { 1: 'ok', 2: 'no', 3: 'maybe' }

	if value:
		return images[value]
	else:
		return ''

class GetPersonDateStatus(template.Node):
	def render(self, context):
		participant = context['participant']
		option = context['option']
		response = participant.get_response(option)
		context['response'] = response
		return ''

@register.tag(name='get_person_status')
def get_the_participants_status(parser, token):
	return GetPersonDateStatus()

