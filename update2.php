<?php

include('../connect.php');



function getBookFilenames() {
	global $pdo;
	$sql = "SELECT image_filename FROM books WHERE Removed > 0";
	$statement = $pdo->prepare($sql);
	$statement->execute();
	$returnArray = array();
    while( ($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false ) {
        array_push($returnArray, $result['image_filename'] );
    }
	$returnString = json_encode($returnArray);
	return $returnString;
}


echo getBookFilenames();

