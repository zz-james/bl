import json
from PIL import Image

class SpriteSheetReader:

  def __init__(self, imageName, tilesize):
    self.spritesheet = Image.open(imageName)
    self.tileSize    = tilesize
    self.margin      = 1

  def getTile(self, tileX, tileY):
    posX = (self.tileSize * tileX) + (self.margin * (tileX + 1))
    posY = (self.tileSize * tileY) + (self.margin * (tileY + 1))
    box = (posX, posY, posX + self.tileSize, posY + self.tileSize)
    return self.spritesheet.crop(box)

reader = SpriteSheetReader('brick-house.png', 32)
tile1  = reader.getTile(6,6)
tile1.show()