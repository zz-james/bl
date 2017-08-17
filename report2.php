<?php

include('../connect.php');

function getRequestedTime(){
  if(!isset($_GET['time'])) { $_GET['time'] = '1473435000'; }
  $time = trim($_GET['time']);
  return filter_var ( $time, FILTER_SANITIZE_NUMBER_INT);
}


$rtime = getRequestedTime();
$urltime = date("Y-m-d H:i:s", $rtime );
echo "<h1>".$urltime."</h1>";
$json = file_get_contents('http://api.bl.uk/IOCT/6806F32C-5E56-4D09-BCCE-A535DE4F2137/'.$urltime);
$data = json_decode($json);

print_r($data);
$filenames    = $data->Shelfmarks;
$details = getBookDetails($filenames);

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


// the message
$msg = "First line of text\nSecond line of text";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

// send email
echo "hello".!!mail("zz.james@gmail.com","My subject",$msg);
