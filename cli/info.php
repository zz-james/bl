<?php
//This function prints a text array as an html list.
function alist ($array) {  
  $alist = "";
  for ($i = 0; $i < sizeof($array); $i++) {
    $alist .= " $array[$i]";
  }
  return $alist;
}
//Try to get ImageMagick "convert" program version number.
exec("convert -version", $out, $rcode);

echo alist($out); 
?>