from django.conf.urls.defaults import *
from django.contrib.auth import views as auth_views

# Uncomment the next two lines to enable the admin:
from django.contrib import admin
admin.autodiscover()

urlpatterns = patterns('',
	(r'(?P<token_uid>\w+).jpg$','planster.djaptcha.views.captcha_image'),
	(r'^rpc/(?P<plan_hash>\w{15})/options/(?P<option_id>\d+)',
		'planster.main.rpc.option'),
	(r'^rpc/(?P<plan_hash>\w{15})/options', 'planster.main.rpc.options'),
	#(r'^rpc/(?P<plan_hash>\w{15})/instructions',
	#	'planster.main.rpc.instructions'),
	#(r'^rpc/(?P<plan_hash>\w{15})/title', 'planster.main.rpc.title'),
	#(r'^rpc/(?P<plan_hash>\w{15})/owner', 'planster.main.rpc.owner'),
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
	#(r'^accounts/profile/$', auth_views.password_reset),
	#(r'^accounts/login/$', 'django.contrib.auth.views.login'),
	#(r'^accounts/logout/$', 'django.contrib.auth.views.logout', { 'next_page': '/'}),
	(r'^accounts/', include('registration.urls')),
	(r'^media/(?P<path>.*)$', 'django.views.static.serve',
		{'document_root': '/home/stefan/projects/planster/trunk/planster/media'}),

	# Example:
	# (r'^planster/', include('planster.foo.urls')),

	# Uncomment the admin/doc line below and add 'django.contrib.admindocs'
	# to INSTALLED_APPS to enable admin documentation:
	# (r'^admin/doc/', include('django.contrib.admindocs.urls')),

	# Uncomment the next line to enable the admin:
	(r'^admin/(.*)', admin.site.root),
)

handler404 = 'planster.main.views.error_404'
handler500 = 'planster.main.views.server_error'
