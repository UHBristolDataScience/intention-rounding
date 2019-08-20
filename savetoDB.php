<?php

//Norman Pang 2019

//defining constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'intentionrounding');

//static variable for the number of beds on ITU
//note if changed here needs changing in the php and vice versa
$numberofbeds = 22;

//static array for the intention rounding criteria. removed bed number category, not needed.
//note if changed here needs changing in the php and vice versa
$categories = array(
  "linesite",
  "biopatch",
  "lineremoval",
  "height",
  "infection",
  "procath",
  "venttube",
  "oohca",
  "vapbundle",
  "nok",
);

//connecting server
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);

//error catch
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

//selecting database
$db_selected = mysqli_select_db($link, DB_NAME);

//printed confirmation of connection to database
if ($result = mysqli_query($link, "SELECT DATABASE()")) {
    $row = mysqli_fetch_row($result);
    printf("database selected is %s.\n", $row[0]);
    mysqli_free_result($result);
}

//getting the intial text box inputs from the web page
$date = $_POST['date'];
$nurse = $_POST['nurse'];
$consultant = $_POST['consultant'];

//function if the bed number is single digits to change it to a 2 digit layout
//to standard the strings of the id to match radio button names
function minTwoDigits($n) {
  if ($n<10){
    $n = "0"."$n";
    return $n;
  } else {
    return $n;
  }
}

//loop that goes through each of the bed numbers and for each bed number another loop
//that cycles through each category and for each data point inserts a new row into
//the sql database
for ($i=1 ; $i<=$numberofbeds ; $i++){

  foreach ($categories as $category){
    $bed = $i;
    $type = $category;
    //getting the value from the radio boxes in the html. the format of the radio names is:
    // 00category  with a 2 digit number in front followed by category type
    $value = $_POST[(minTwoDigits($i).$category)];

    //if $value is undefined because the radio is blank, sets it to NA
    if (!isset($value)){
      $value = "NA";
    }

    $sql = "INSERT INTO intentiondata (date, nurse, consultant, bed, type, value)
            VALUES ('$date', '$nurse', '$consultant', '$bed', '$type', '$value')";
    //error catch
    if(mysqli_query($link, $sql)){
          echo "Records inserted successfully.";
      } else{
          echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
      }

  }
}

mysqli_close($link);

?>
