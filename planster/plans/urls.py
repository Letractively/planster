from django.conf.urls.defaults import *

urlpatterns = patterns('',
	(r'(?P<token_uid>\w+).jpg$','djaptcha.views.captcha_image'),
	(r'^rpc/(?P<plan_hash>\w{15})/options/(?P<option_id>\d+)',
		'plans.rpc.option'),
	(r'^rpc/(?P<plan_hash>\w{15})/options', 'plans.rpc.options'),
	(r'^rpc/(?P<plan_hash>\w{15})/people/(?P<person_id>\d+)/responses',
		'plans.rpc.responses'),
	(r'^rpc/(?P<plan_hash>\w{15})/people/(?P<person_id>\d+)',
		'plans.rpc.person'),
	(r'^rpc/(?P<plan_hash>\w{15})/people', 'plans.rpc.people'),
	(r'^rpc/(?P<plan_hash>\w{15})', 'plans.rpc.plan'),
	(r'^rpc', 'plans.rpc.plans'),
	(r'^(?P<plan_id>\w{15})', 'plans.views.plan'),
	(r'^(?P<plan_hash>\w{12})', 'plans.views.legacy'),
	(r'^$', 'plans.views.index'),
	(r'^create$', 'plans.views.create'),
	(r'^export$', 'plans.views.export'),
	(r'^accounts/profile/$', 'plans.views.profile'),
	(r'^nojs$', 'plans.views.nojs'),
)

handler404 = 'plans.views.error_404'
handler500 = 'plans.views.server_error'
