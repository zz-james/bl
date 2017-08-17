import json
from PIL import Image

#make sure you have checked the maximum x coord in the json and the image is large enough
print('loading image...')
background = Image.open('output-50k_mix-03FixShd.jpeg')


print('loading json...')
with open('book_data.json') as f:
  books = json.load(f)

# print(books[0])

for i, shelf in enumerate(books):

  maxbook = len(shelf) - 1 # -1 for zero index offset, another -1 because we use the last element as a right for the last rect

  print(i)
  for x in range(0, maxbook):




#    print ('row '+str(i))
#    print (left, top, right, bottom)
    if(shelf[x]['visible'] == 0):
      tile   = Image.open('background_slices/'+str(i)+'_'+str(x)+'.jpg')

      left   = shelf[x]['x']
      right  = (shelf[x+1]['x'])
      top    = (1500 * i)
      bottom = 1500 * (i+1)
      print(i, x, top, left, right, bottom)
      background.paste(tile, (left, top, right, bottom))


# for datum in data:
#   print((datum['x']))
#   print("------------")

# lefts = [print(datum['x']) for datum in data]
# # print(lefts)

# for datum in data:
#   print(datum['x'])
background.save('new_output-50k_mix-03FixShd.jpg')