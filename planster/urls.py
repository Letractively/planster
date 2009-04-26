from django.conf.urls.defaults import *

# Uncomment the next two lines to enable the admin:
from django.contrib import admin
admin.autodiscover()

urlpatterns = patterns('',
	#(r'^rpc/(?P<plan_hash>\w{15})/items', 'planster.main.rpc.items'),
	# TODO: options? items? wtf?
	(r'^rpc/(?P<plan_hash>\w{15})/options', 'planster.main.rpc.options'),
	(r'^rpc/(?P<plan_hash>\w{15})/instructions',
		'planster.main.rpc.instructions'),
	(r'^rpc/(?P<plan_hash>\w{15})/title', 'planster.main.rpc.title'),
	(r'^rpc/(?P<plan_hash>\w{15})/owner', 'planster.main.rpc.owner'),
	#(r'^rpc/.{15}/items', 'planster.main.rpc.items'),
	(r'^rpc', 'planster.main.rpc.plan'),
	# Example:
	# (r'^planster/', include('planster.foo.urls')),

	# Uncomment the admin/doc line below and add 'django.contrib.admindocs'
	# to INSTALLED_APPS to enable admin documentation:
	# (r'^admin/doc/', include('django.contrib.admindocs.urls')),

	# Uncomment the next line to enable the admin:
	(r'^admin/(.*)', admin.site.root),
)
