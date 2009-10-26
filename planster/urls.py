from django.conf.urls.defaults import *
from django.contrib.auth import views as auth_views

# Uncomment the next two lines to enable the admin:
from django.contrib import admin
admin.autodiscover()

urlpatterns = patterns('',
	(r'^', include('main.urls')),
	(r'^admin/(.*)', admin.site.root),
	(r'^accounts/', include('registration.urls')),
	(r'^favicon\.ico$', 'django.views.generic.simple.redirect_to', {'url': 'http://static.planster.net/img/favicon.ico'}),
	(r'^media/(?P<path>.*)$', 'django.views.static.serve',
		{'document_root': '/home/stefan/projects/planster/trunk/planster/media'}),

	# Example:
	# (r'^planster/', include('planster.foo.urls')),

	# Uncomment the admin/doc line below and add 'django.contrib.admindocs'
	# to INSTALLED_APPS to enable admin documentation:
	# (r'^admin/doc/', include('django.contrib.admindocs.urls')),
)
