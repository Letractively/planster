from django.conf.urls.defaults import *
urlpatterns = patterns('',
     (r'(?P<token_uid>\w+).jpg$','djaptcha.views.captcha_image'),
)

