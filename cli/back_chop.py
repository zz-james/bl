import json
from PIL import Image

#make sure you have checked the maximum x coord in the json and the image is large enough

print('loading image...')
background = Image.open('BasementComp-03.jpg')

print('loading json...')
with open('book_data.json') as f:
  books = json.load(f)

print('cropping images...')
# print(books[0])

for i, shelf in enumerate(books):

  maxbook = len(shelf) - 1 # -1 for zero index offset, another -1 because we use the last element as a right for the last rect


  for x in range(0, maxbook):
    left   = shelf[x]['x']
    right  = (shelf[x+1]['x'])
    top    = (1500 * i)
    bottom = 1500 * (i+1) # have to put this in as things have shifted in the hi res version
    print ('row '+str(i))
    print (left, top, right, bottom)
    croppedIm = background.crop((left, top, right, bottom))
    croppedIm.save('background_slices/'+str(i)+'_'+str(x)+'.jpg',dpi = [96,96])

# for datum in data:
#   print((datum['x']))
#   print("------------")

# lefts = [print(datum['x']) for datum in data]
# # print(lefts)

# for datum in data:
#   print(datum['x'])