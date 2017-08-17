<?php

include('../connect.php');

function test() {
    global $pdo;
    $sql = 'select * from books';
    $statement = $pdo->prepare($sql);
    $statement->execute();

    // results
    while( ($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false ) {
        echo $result['title']."<br />";
    }
}

function sqlrand() {
    global $pdo;
    $sql = '(SELECT ROUND( RAND() * (SELECT COUNT(*) FROM books) ) AS rando)';
    $statement = $pdo->prepare($sql);
    $statement->execute();

    // result should be only one
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $num = (int)$result['rando'];

    $sql = 'SELECT image_filename FROM books LIMIT '.$num.',1';
    $statement = $pdo->prepare($sql);
    $statement->execute();
    // result should be only one
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $book = $result['image_filename'];
	$returnArray = [$book];
	echo json_encode($returnArray);
}


function checkABRS() {
   $json = file_get_contents('http://api.bl.uk/IOCT/6806F32C-5E56-4D09-BCCE-A535DE4F2137/2016-05-05%2015:00:00.000');
   echo $json;
   echo PHP_EOL;
}


//checkABRS();
sqlrand();