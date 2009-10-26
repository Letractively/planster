from django.conf.urls.defaults import *

urlpatterns = patterns('',
	(r'(?P<token_uid>\w+).jpg$','planster.djaptcha.views.captcha_image'),
	(r'^rpc/(?P<plan_hash>\w{15})/options/(?P<option_id>\d+)',
		'planster.main.rpc.option'),
	(r'^rpc/(?P<plan_hash>\w{15})/options', 'planster.main.rpc.options'),
	(r'^rpc/(?P<plan_hash>\w{15})/people/(?P<person_id>\d+)/responses',
		'planster.main.rpc.responses'),
	(r'^rpc/(?P<plan_hash>\w{15})/people/(?P<person_id>\d+)',
		'planster.main.rpc.person'),
	(r'^rpc/(?P<plan_hash>\w{15})/people', 'planster.main.rpc.people'),
	(r'^rpc/(?P<plan_hash>\w{15})', 'planster.main.rpc.plan'),
	(r'^rpc', 'planster.main.rpc.plans'),
	(r'^(?P<plan_id>\w{15})', 'planster.main.views.plan'),
	(r'^(?P<plan_hash>\w{12})', 'planster.main.views.legacy'),
	(r'^$', 'planster.main.views.index'),
	(r'^create$', 'planster.main.views.create'),
	(r'^export$', 'planster.main.views.export'),
	(r'^accounts/profile/$', 'planster.main.views.profile'),
	(r'^nojs$', 'planster.main.views.nojs'),
)

handler404 = 'planster.main.views.error_404'
handler500 = 'planster.main.views.server_error'
