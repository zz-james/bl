<?php

include('../connect.php');

$dtime = '2016-09-09 16:30:00.000000';




function checkABRS() {
    global $dtime;
  /*
  this endpoint format is
  /IoCTQuery.svc/query/sdhriuhrkerr/2016-05-05%2015:00:00.000
  http://{servername}/IoCTQuery.svc/query/{groupName}/{sinceDateTime}
  {sinceDateTime} value is yyyy-MM-dd HH:mm:ss.ms */
  $json = file_get_contents('http://api.bl.uk/IOCT/6806F32C-5E56-4D09-BCCE-A535DE4F2137/'.$dtime);
  $data = json_decode($json);
  $filenames    = $data->Shelfmarks;
  $returnString = getBookFilenames($filenames);
  return $returnString;
}

function getBookFilenames($removedArray) {
  global $pdo;
  $sql = "SELECT image_filename FROM books WHERE ";
  foreach ($removedArray as $shelfmark) {
    $sql .= "transcribed_shelfmark='".$shelfmark."' or ";
  }
  $sql .= "false";
  $statement = $pdo->prepare($sql);
  $statement->execute();
  $returnArray = [];
    while( ($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false ) {
        array_push($returnArray, $result['image_filename'] );
    }
  $returnString = json_encode($returnArray);
  return $returnString;
}


echo checkABRS();