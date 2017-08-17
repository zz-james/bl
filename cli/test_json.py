import json

#make sure you have checked the maximum x coord in the json and the image is large enough

print('loading json...')
with open('book_data.json') as f:
  books = json.load(f)

# print(books[0])

for i, shelf in enumerate(books):

  maxbook = len(shelf) - 1 # -1 for zero index offset, another -1 because we use the last element as a right for the last rect


  for x in range(0, maxbook):




#    print ('row '+str(i))
#    print (left, top, right, bottom)
    if(shelf[x]['visible'] == 0):
      left   = shelf[x]['x']
      right  = (shelf[x+1]['x'])-1
      top    = (1466 * i) + 244
      bottom = 1466 * (i+1) + 244 # have to put this in as things have shifted in the hi res version
      print(i, x, top, left, right, bottom)

# for datum in data:
#   print((datum['x']))
#   print("------------")

# lefts = [print(datum['x']) for datum in data]
# # print(lefts)

# for datum in data:
#   print(datum['x'])