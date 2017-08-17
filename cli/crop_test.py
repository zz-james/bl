from PIL import Image

class SpriteSheetWriter:

  def __init__(self, tileSize, spriteSheetSize):
    self.tileSize = tileSize
    self.spriteSheetSize = spriteSheetSize
    self.spriteSheet = Image.new("RBGA", (self.spriteSheetSize,self.spriteSheetSize), (0,0,0,0))
    self.tileX = 0
    self.tileY = 0
    self.margin = 1

  def getCurPos(self):
    self.posX = (self.tileSize * self.tileX) + (self.margin * (self.tileX + 1))
    self.posY = (self.tileSize * self.tileY) + (self.margin * (self.tileY + 1))

    if(self.posX + self.tileSize > self.spriteSheetSize):
      self.tileX = 0
      self.tileY = self.tileY + 1
      self.getCurPos()

    if(self.posY + self.tileSize < self.spriteSheetSize):
      raise Exception('image does not fit within sprite sheet')

  def addImage(self, image):
    self.getCurPos()
    destBox = (self.posX, self.posY, self.posX + image.size[0], self.posY + image.size[1])
    self.spriteSheet.paste(image, destBox)
    self.tileX = self.tileX + 1

  def show(self):
    self.spriteSheet.show()




croppedIm = catIm.crop((335, 345, 565, 560))
croppedIm.save('cropped.png')