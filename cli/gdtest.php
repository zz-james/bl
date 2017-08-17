<?php
echo 'starting....';

if (!extension_loaded('gd')){
  throw new RuntimeException('The GD extension for PHP is not available.');
}

// making changes to existing images

$books = imagecreatefromjpeg('bookshelves_half_resized.jpg');
list($width, $height) = getimagesize('bookshelves_half_resized.jpg');
// Load
$booksScaled = imagecreatetruecolor(640, 640);


// Resize
imagecopyresized($booksScaled, $books, 0, 0, 0, 0, 640, 640, $width, $height);



//$booksScaled = imagescale( $books , 640 );

$filename = 'scaled.png';
$to       = '/Users/james/Projects/htdocs/bl/maptest/cli/tiles/'.$filename;
$compress = 4;

$success = imagepng ( $booksScaled, $to, $compress );

echo 'output was: '.$success;