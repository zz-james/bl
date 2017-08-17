<?php
/**
 * @package PHP MapTiler, Simple Map Tiles Generator
 * @version 1.1 (2013.05.13)
 * @author  Fedik getthesite at gmail.com
 * @link    http://www.getsite.org.ua
 * @license GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

class MapTiler
{
  /**
   * image path
   * @var string
   */
  protected $image_path = null;

  /**
   * tiles path
   * @var string
   */
  protected $tiles_path = null;

  /**
   * tile size
   * @var int
   */
  protected $tile_size = 256;

  /**
   * Store structure, examples: zoom/x/y, zoom/x-y
   * file name format, can contain the path separator
   * extension (eg: '.jpg') will add automaticaly depend of format option
   * @var string for sprintf()
   */
  protected $store_structure = '%d/%d/%d';

  /**
   * force tile generation, even if tile already exist
   * @var bool
   */
  protected $force = false;

  /**
   * http://www.maptiler.org/google-maps-coordinates-tile-bounds-projection/
   * if true - tiles will generates from top to bottom
   * @var bool
   */
  protected $tms = true;

  /**
   * fill color can be transparent for png
   * @var string
   */
  protected $fill_color = 'transparent';

  /**
   * zoom min
   * @var int
   */
  protected $zoom_min = 2;
  
  /**
   * zoom max
   * @var int
   */
  protected $zoom_max = 8;

  /**
   * for prevent image scalling up
   * if image size less than need for zoom level
   * @var int - max zoom level, when scalling up is allowed
   */
  protected $scaling_up = 0;

  /**
   * Imagic filter for resizing
   * http://www.php.net/manual/en/imagick.constants.php
   * Imagick::FILTER_POINT - fast with bad quality
   * Imagick::FILTER_CATROM - good enough
   */
  //protected $resize_filter = Imagick::FILTER_POINT;

  /**
   * image format used for store the tiles: jpeg or png
   * http://www.imagemagick.org/script/formats.php
   * @var string
   */
  protected $format = 'png';

  /**
   * quality of the saved image in jpeg format
   * @var int
   */
  protected $quality_jpeg = 80;

  /**
   * ImageMagick tmp folder,
   * Can be changed in case if system /tmp have not enough free space
   * @var string
   */
  protected $imagick_tmp = null;

  /**
   * array with profiler class and method for call_user_func_array
   * @var array
   */
  protected $profiler_callback = null;

  /**
   * Undocumented variable
   * main image is the inital full fize image which gets resized and chopped to create all the tiles
   * @var Imagick object
   */
  protected $main_image = null; // 




  /**
   * Class constructor.
   */
  public function __construct($image_path, $options = array())
  {
    // Verify that imagick support for PHP is available.
    if (!extension_loaded('imagick')){
      throw new RuntimeException('The Imagick extension for PHP is not available.');
    }

    $this->image_path = $image_path;
    $this->setOptions($options);

    //if new tmp folder given
    if($this->imagick_tmp && is_dir($this->imagick_tmp)){
      putenv('MAGICK_TEMPORARY_PATH=' . $this->imagick_tmp);
    }



  }

  /**
   * set options
   * @param array $options
   */
  public function setOptions($options) {
    foreach($options as $k => $value){
      $this->{$k} = $value;
    }
  }

  /**
   * get options
   * @return array $options
   */
  public function getOptions() {
    return get_object_vars($this);
  }







  /**
   * run make tiles process
   * @param bool $clean_up - whether need to remove a zoom base images
   */
  public function process($clean_up = false){
    $this->profiler('MapTiler: Process. Start');
    $this->prepareImages(); //prepare each zoom lvl base images
    $this->profiler('MapTiler: Process. End');
  }


  /**
   * process images recursive function which runs the image prep pipeline once
   * for each zoom level from 8 to zero
   *
   * @param [type] $zoom
   * @param [type] $zoom_min
   * @return void
   */
  protected function processImage($zoom, $zoom_min) {
    if($zoom < $zoom_min) {
      return;
    } else {
      //get image size
      $main_size_w = $this->main_image->getimagewidth();
      $main_size_h = $this->main_image->getImageHeight();
      $this->profiler('Main Image size: '.$main_size_w);
      $this->profiler('Image for zoom level: '.$zoom);

      // pipeline
      $nextZoom = $this->expandToExtent( $this->halfImageSize( $this->createTilesForZoom($zoom) ) );

      $this->processImage($nextZoom, $zoom_min);
    }
  }


  /**
   * make the images
   * @param int $min - min zoom lvl
   * @param int $max - max zoom lvl
   */
  public function prepareImages($min = null, $max = null){
    //prepare zoom levels
    if($min){
      $max = !$max ? $min : $max;
      $this->zoom_min = $min;
      $this->zoom_max = $max;
    }

    //load main image
    if(!is_file($this->image_path) || !is_readable($this->image_path)){
      throw new RuntimeException('Cannot read image '.$this->image_path);
    }
    $this->profiler('MapTiler: Loading Main Image: '.$this->image_path);
    
    $this->main_image = $this->loadImage($this->image_path);
    $this->main_image->setImageFormat($this->format);

    $this->profiler('MapTiler: Main Image loaded');
    
    $ext = $this->getExtension();

    $this->profiler('--------------------------');
    // recursive function zoom min goes from 8 to 0 because 8 is max zoom (biggest).
    $this->processImage($this->zoom_max, $this->zoom_min);
      //prepare base images for each zoom lvl
      
      // $img_size_w = $main_size_w / pow(2, $this->zoom_max - $i);
      // $img_size_h = $img_size_w;
      // $this->profiler('Image for zoom level: '.$i);
      // $this->profiler('Image size: '.$img_size_w);
      // $num_tiles = floor($img_size_w / 256)+1;
      // $this->profiler('number of tiles: '.$num_tiles);
      // $total_size = $num_tiles * 256;
      // $this->profiler('size with trans pixels: '.$total_size);

      // $this->profiler('filling free space for zoom level: '.$i);
      // $expandedImage = $this->fillFreeSpace($main_image, $total_size, $total_size);
      // the image pipeline looks like this
      // $this->profiler('saving image: '.$i);

      // $savepath = "/home/james/Desktop/elastic/test.png"; // we don't actually save this
      // $this->imagesave($expandedImage,$savepath);
      // half the size of the image
      // expand the image by filling out with transparent until a multiple of 256px
      // cut the image into 256px tiles
      // save each tile as a side effect
      // back to the start of the pileline
      // until we reach the zoom min.

      // free resurce, destroy imagick object
      // if($lvl_image) $this->unloadImage($lvl_image);

      
  }



  /**
   * make tiles for given zoom level
   * @param int $zoom
   */
  public function createTilesForZoom($zoom) {
    $path = $this->tiles_path.'/'.$zoom;
    //base image
    $ext = $this->getExtension();
    $file = $this->tiles_path.'/'.$zoom.'.'.$ext;



    //get image size
    $image_w = $this->main_image->getimagewidth();
    $image_h = $this->main_image->getImageHeight();

    //count by x,y -hm, ceil or floor?
    $x = ceil($image_w / $this->tile_size);
    $y = ceil($image_h / $this->tile_size);

    $this->profiler('  '.$x.' tiles accross');
    $this->profiler('  '.$y.' tiles down');

    //tile width, height
    $w = $this->tile_size;
    $h = $w;

    //crop cursor - this is the rect that the tile is 
    $crop_x = 0;
    $crop_y = 0;


     $this->profiler('Begin looping over image and creating tiles for level '.$zoom);

    //by x
    for($ix = 0; $ix < $x; $ix++){
      $crop_x = $ix * $w;
      $this->profiler('  X is:'.$ix);
      //by y
      for($iy = 0; $iy < $y; $iy++){
        //full file path
        $lvl_file = $this->tiles_path.'/'.sprintf($this->store_structure, $zoom, $ix, $iy).'.'.$ext;
        
        //$this->profiler('  File Path:'.$lvl_file);
        
        //check if already exist
        if(!$this->force && is_file($lvl_file)){
          continue;
        }

        //$crop_y = $this->tms? $image_h - ($iy + 1)* $h : ;

        $crop_y = $iy * $h;

        //crop
        //$this->profiler('  Cloning Image');
        $tile = clone $this->main_image;

        //$this->profiler('  Cropping Image to create Tile');
        $tile->cropImage($w, $h, $crop_x, $crop_y);

        $tile->setImagePage($w, $h, 0, 0); //sets the tile register location
        //check if image smaller than we need
        // if($tile->getImageWidth() < $w || $tile->getimageheight() < $h){
        //   $this->fillFreeSpace($tile, $w, $h, true);
        // }

        //save
        //$this->profiler('  Saving Tile');
        $this->imageSave($tile, $lvl_file);
        //$this->profiler('  Unloading Tile');
        $this->unloadImage($tile);
      }
    }

    $this->profiler('MapTiler: Created Tiles for zoom level: '. $zoom);
    return $zoom;
  }

protected function halfImageSize($zoom) {
  $next_image_w = $this->main_image->getimageWidth() / 2;
  $next_image_h = $this->main_image->getimageHeight() / 2;

  $this->profiler('Next Image Width : '. $next_image_w);


  $this->imageFit($this->main_image, $next_image_w, $next_image_h );
  return $zoom - 1;
}


protected function expandToExtent($zoom) {
  $adjust = ($this->main_image->getimageWidth() % 256) ? 1 : 0;
  $num_tiles = floor($this->main_image->getimageWidth() / 256)+$adjust;
  $this->profiler('number of tiles: '.$num_tiles);

  $extent_size = $num_tiles * 256;
  $this->profiler('size with trans pixels: '.$extent_size);

  $this->profiler('filling free space for zoom level: '.$zoom);
  $this->fillFreeSpace($this->main_image, $extent_size, $extent_size);

  return $zoom;
}

/** ===================== image utilities ===================== **/



  /**
   * Fit image in to given size
   * http://php.net/manual/en/imagick.resizeimage.php
   * @param resurce $image Imagick object
   * @param int $w width
   * @param int $h height
   *
   * @return resurce imagick object
   */
  protected function imageFit($image, $w, $h) {
    //resize - works slower but have a better quality
    //$image->resizeImage($w, $h, $this->resize_filter, 1, true);

    //scale - work fast, but without any quality configuration
    $image->scaleImage($w, $h, true);

    return $image;
  }





  /**
   * Put image in to rectangle and fill free space
   *
   * @param resurce $image Imagick object
   * @param int $w width
   * @param int $h height
   *
   * @return resurce imagick object
   */
  protected function fillFreeSpace($image, $w, $h) {
    $image->setImageBackgroundColor($this->fill_color);
    $image->extentImage(
        $w, $h,
        0, 0
    );

    return $image;
  }




/** ===================== file i/o stuff ===================== **/


  /**
   * load image and return imagic resurce
   * @param string $path
   * @return resource Imagick
   */
  protected function loadImage($path = null) {
    return new Imagick($path);
  }


  /**
   * Destroys the Imagick object
   * @param resurce $image Imagick object
   * @return bool
   */
  protected function unloadImage($image) {
    $image->clear();
    return $image->destroy();
  }


  /**
   * save image in to destination
   * @param resurce $image
   * @param string $name
   * @param string $dest full path with file name
   */
  protected function imageSave($image, $dest){
    //prepare folder
    $this->makeFolder(dirname($dest));

    //prepare to save
    if($this->format == 'jpeg') {
      $image->setCompression(Imagick::COMPRESSION_JPEG);
      $image->setCompressionQuality($this->quality_jpeg);
    }

    //save image
    if(!$image->writeImage($dest)){
      throw new RuntimeException('Cannot save image '.$dest);
    }

    return true;
  }


  /**
   * create folder
   * @param string $path folder path
   */
  protected function makeFolder($path, $mode = 0755) {
    //check if already exist
    if(is_dir($path)){
      return true;
    }
    //make folder
    if(!mkdir($path, $mode, true)) {
      throw new RuntimeException('Cannot crate folder '.$path);
    }
    return true;
  }


  /**
   * return file extension depend of given format
   * @param string $format - output format used in Imagick
   */
  public function getExtension($format = null){
    $format = $format ? $format : $this->format;
    $ext = '';

    switch (strtolower($format)) {
      case 'jpeg':
      case 'jp2':
      case 'jpc':
      case 'jxr':
        $ext = 'jpg';
        break;
      case 'png':
      case 'png00':
      case 'png8':
      case 'png24':
      case 'png32':
      case 'png64':
        $ext = 'png';
        break;
    }
    return $ext;
  }



/** ===================== logging stuff ===================== **/

  /**
   * profiler function
   * @param string $note
   */
  protected function profiler($note) {
    echo date('Y-m-d H:i:s')." ".$note.PHP_EOL;
    if($this->profiler_callback) {
      call_user_func_array($this->profiler_callback, array($note));
    }
  }
}
