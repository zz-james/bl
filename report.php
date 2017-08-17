<?php

include('../connect.php');

$dtime = '2016-09-09 18:30:00.000000';




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
  $details = getBookDetails($filenames);
  echo '<b>Query String is:</b>';
  echo '<br />';
  echo 'http://api.bl.uk/IOCT/6806F32C-5E56-4D09-BCCE-A535DE4F2137/'.$dtime;
  echo '<br />';
  echo '<i>Which means \'list all books since 4.30pm on 9th September\'</i>';
  echo '<br />';
  echo '<br />';
  echo '<b>Raw JSON return sting:</b>';
  echo '<br />';
  echo $json;
  echo '<br />';
  echo '<br />';
  echo '<b>Shelfmarks:</b>';
  echo '<br />';
  print_r($filenames);
  echo '<br />';
  echo '<br />';
  echo '<b>Details:</b>';
  echo '<table border="1"><tr>';
  foreach ($details[0] as $key=>$value) {
    echo '<th>'.$key.'</th>';
  }
  echo '</tr>';
  foreach ($details as $key=>$value) {
    echo '<tr>';
    foreach($value as $thekey=>$thevalue) {
      echo '<td>'.$thevalue.'</td>';
    }
    echo '</tr>';
  }


  echo '</tr>';
  echo '</table>';
}

function getBookDetails($removedArray) {
  global $pdo;
  $sql = "SELECT * FROM books WHERE ";
  foreach ($removedArray as $shelfmark) {
    $sql .= "transcribed_shelfmark='".$shelfmark."' or ";
  }
  $sql .= "false";
  $statement = $pdo->prepare($sql);
  $statement->execute();
  $result = $statement->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}


checkABRS();