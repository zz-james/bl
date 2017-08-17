


<?php


// convert new_output-50k_mix-03FixShd.jpg -monitor -background none -gravity NorthEast -extent 49664x49664 r_new_output-50k_mix-03FixShd.png
// convert -monitor -crop 256x256 +repage r_new_output-50k_mix-03FixShd.png tiles_%d.png


//convert dragon.gif    -resize 64x64  resize_dragon.gif

$tile_width   = 256;
$tile_height  = 256;
$image_width  = 49664;
$image_height = 49664;
$n            = 0;


// (image_height / tile_height)^2 --> cos our image is square

$total_tiles = 37636;

$tiles_per_row = $image_width / $tile_width;

$col = 0;
$row = 0;


for($i = $n; $i < $total_tiles; $i++) {

  $filename = "tiles_".$i.".png";
  $target = "map_".$col."_".$row.".png";

  echo "rename ".$filename." to ".$target. PHP_EOL;

  shell_exec('mv '.$filename." ".$target);

  $col++;

  if($col == $tiles_per_row) {
    $row++;
    $col=0;
  }
}

