from django.http import HttpResponse, Http404
from djaptcha.models import CaptchaRequest
from cStringIO import StringIO
import random
import Image,ImageDraw,ImageFont

# You need to get the font from somewhere and have it accessible by Django
# I have it set in the djaptcha's settings dir 
from djaptcha.settings import FONT_PATH,FONT_SIZE


def captcha_image(request,token_uid):
    """
    Generate a new captcha image.
    """
    try:
    	captcha = CaptchaRequest.objects.get(uid=token_uid)
    except CaptchaRequest.DoesNotExist:
	raise Http404;
    text = captcha.text
    #TODO: Calculate the image dimensions according to the given text.
    #      The dimensions below are for a "X+Y" text
    image = Image.new('RGB', (40, 23), (255, 255, 255))
    # You need to specify the fonts dir and the font you are going to usue
    font = ImageFont.truetype(FONT_PATH,FONT_SIZE)
    draw = ImageDraw.Draw(image)
    # Draw the text, starting from (2,2) so the text won't be edge
    draw.text((2, 2), text, font = font, fill = (153, 204, 25))
    # Saves the image in a StringIO object, so you can write the response
    # in a HttpResponse object
    out = StringIO()
    image.save(out,"JPEG")
    out.seek(0)
    response = HttpResponse()
    response['Content-Type'] = 'image/jpeg'
    response.write(out.read())
    return response 
