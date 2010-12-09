BASE_PATH = '/home/stefan/projects/planster/trunk/planster'

import os, sys
os.environ['DJANGO_SETTINGS_MODULE'] = 'settings'

import django.core.handlers.wsgi
application = django.core.handlers.wsgi.WSGIHandler()

if BASE_PATH not in sys.path:
	sys.path.append(BASE_PATH)
