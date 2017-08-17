<?php

include("maptiler.php");

//init 
$map_tiler = new MapTiler('/Users/james/Projects/htdocs/bl/maptest/cli/new_base_fullsize.png', array(
  'tiles_path' => '/Users/james/Projects/htdocs/bl/maptest/cli/tiles/',
  'zoom_max' => 8,
  'zoom_min' => 2
));
//execute
try {
  $map_tiler->process(true);
} catch (Exception $e) {
  echo $e->getMessage();
  echo $e->getTraceAsString();
}