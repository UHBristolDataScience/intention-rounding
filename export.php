<?php

//A PHP script to export the current databse as a csv file

//include database configuration file
include 'config.php';

//SQL query to retreive all intentiondata data by ascending ID
$query = "SELECT * FROM intentiondata ORDER BY ID ASC";

//delimiter for the CSV format
$delimiter = ",";
$filename = "intentionroundingdata.csv";

//create a file pointer
$f = fopen('php://memory', 'w');

//set column headers for csv file
$categories = array(
  "ID",
  "date",
  "nurse",
  "consultant",
  "bed",
  "type",
  "value",
);

fputcsv($f, $categories, $delimiter);

//if loop to continue to while loop if connects
if($result = mysqli_query($link, $query)){
  //while loop to go through the database and pull data and insert it into the csv file
  while($row = mysqli_fetch_array($result)){
    $lineData = array($row['ID'], $row['date'], $row['nurse'], $row['consultant'], $row['bed'], $row['type'], $row['value']);
    fputcsv($f, $lineData, $delimiter);
  }
}

//move back to beginning of file
fseek($f, 0);

//set headers to download file rather than displayed
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

//output all remaining data on a file pointer
fpassthru($f);

exit;

?>
