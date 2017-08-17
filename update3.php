<?php

include('../connect.php');

// function getRequestedTime(){
//   if(!isset($_GET['time'])) { $_GET['time'] = '1473435000'; }
//   $time = trim($_GET['time']);
//   return filter_var ( $time, FILTER_SANITIZE_NUMBER_INT);
// }


$rtime   = time () - (30 * 60); //getRequestedTime();
$urltime = date("Y-m-d H:i:s", $rtime );
$json    = file_get_contents('http://api.bl.uk/IOCT/6806F32C-5E56-4D09-BCCE-A535DE4F2137/'.$urltime);
$data    = json_decode($json);
echo 'unix time is: '.$rtime.PHP_EOL;
echo 'the time is: '.$urltime.PHP_EOL;
print_r($data);

$bob = $data ? timestamp($data->Shelfmarks, $rtime) : print('<h2>Failed to update database as no data</h2>');



function timestamp($shelfMarksArray, $requesedTime) {
  global $pdo;

  $whereClause = "Removed = 0 and (";
  foreach ($shelfMarksArray as $shelfmark) {
    $whereClause .= "transcribed_shelfmark='".$shelfmark."' or ";
  }
  $whereClause .= "false)";

  $sql = "UPDATE books SET Removed = ".$requesedTime." WHERE ".$whereClause;

  $statement = $pdo->prepare($sql);
  $statement->execute();
  $rows = $statement->rowCount();
  echo $rows." effected".PHP_EOL;
  timeStampMe($requesedTime);
 // return 'yay';
}

function timeStampMe($requesedTime) {
  global $pdo;
  $sql = "UPDATE timestamp SET time = ".$requesedTime;
  $statement = $pdo->prepare($sql);
  $statement->execute();
  echo $sql;
}

// function checkABRS() {
//     global $dtime;
//   /*
//   this endpoint format is
//   /IoCTQuery.svc/query/sdhriuhrkerr/2016-05-05%2015:00:00.000
//   http://{servername}/IoCTQuery.svc/query/{groupName}/{sinceDateTime}
//   {sinceDateTime} value is yyyy-MM-dd HH:mm:ss.ms */
//   $json = file_get_contents('http://api.bl.uk/IOCT/6806F32C-5E56-4D09-BCCE-A535DE4F2137/'.$dtime);

//     // moment X is gone. 
//     $momentY = date('Y-m-d H:i:s.u');
//   updateTime($momentY);
//   $data = json_decode($json);
//   $filenames = $data->Shelfmarks;
//   $returnString = getBookFilenames($filenames);
//   return $returnString;
// }

// function getBookFilenames($removedArray) {
//   global $pdo;
//   $sql = "SELECT image_filename FROM books WHERE ";
//   foreach ($removedArray as $shelfmark) {
//     $sql .= "transcribed_shelfmark='".$shelfmark."' or ";
//   }
//   $sql .= "false";
//   $statement = $pdo->prepare($sql);
//   $statement->execute();
//   $returnArray = [];
//     while( ($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false ) {
//         array_push($returnArray, $result['image_filename'] );
//     }
//   $returnString = json_encode($returnArray);
//   return $returnString;
// }


// echo checkABRS();

// function resetTime() {
//   global $dtime;
//   // here we define the first momentX as half past 4 on 9th September 2016.
//   $dtime = '2016-09-09 16:30:00.000000'; // Save date in database
//   makeTime($dtime);
// }

// function getTime() {
//   $time = $_GET['time'];

// }
  // global $pdo;
  // $sql = 'SELECT time FROM timestamp';
  // $statement = $pdo->prepare($sql);
  // $statement->execute();
  // $result = $statement->fetch(PDO::FETCH_ASSOC); // only one row
  // return $result['time'];
// function makeTime($time) {
//   global $pdo;
//   $sql = 'INSERT INTO timestamp (time) VALUES (:time)';
//   $statement = $pdo->prepare($sql);
//   $statement->bindValue(':time', $time, PDO::PARAM_STR );
//   $statement->execute();
// }

// function updateTime($time) {
//   global $pdo;
  
//   $sql = 'UPDATE timestamp SET time = :time';
//   $statement = $pdo->prepare($sql);
//   $statement->bindValue(':time', $time, PDO::PARAM_STR );
//   $statement->execute();
//   return false;
// }