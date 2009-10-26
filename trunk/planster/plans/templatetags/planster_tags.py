from django import template

register = template.Library()

@register.simple_tag
def get_status(option, participant):
	values=[ 'unknown', 'yes', 'no', 'maybe' ]
	value = participant.getResponse(option)
	return values[value]
	#return {'status': person.getResponse(option)}
