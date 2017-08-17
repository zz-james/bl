
from PIL import Image

#http://pillow.readthedocs.io/en/3.4.x/reference/Image.html#PIL.Image.Image.paste

print('loading image...')
background = Image.open('output-50k_mix-03FixShd.jpeg') #Image.open('BasementComp-03.jpg')


resizedIm = background.resize((25000,25000)) 
resizedIm.save('bookshelves_half_resized.jpg')
