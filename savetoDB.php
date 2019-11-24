<?php

//Norman Pang 2019

//including file with mySQL database config information
include "config.php";

//static variable for the number of beds on ITU
//note if changed here needs changing in the php and vice versa
//note there is code later to remove bed number 13. '$numberofbeds' represents the highest bed number.
$numberofbeds = 22;


//code to select the categories sql table and fetch the codenames of the categories and put them
//into an array for use in saving
$sql = "SELECT * FROM categories ORDER BY ID ASC";
if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){

      $categories = array();

            while($row = mysqli_fetch_array($result)){
                array_push($categories, $row['codename']);
            }

        // Free result set
        mysqli_free_result($result);
    } else{
        echo "<p class='lead'><em>No records were found.</em></p>";
    }
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}


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
    //concatenation like a bitch
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

  //since there's no bed 13
  if($i!=13){

    foreach ($categories as $category){
      $bed = $i;
      $type = $category;
      //getting the value from the radio boxes in the html. the format of the radio names is:
      // 00category  with a 2 digit number in front followed by category type

      //if no value is set in the html table individual cell, then will default the value to NA
      if (isset($_POST[(minTwoDigits($i).$category)])){
        $value = $_POST[(minTwoDigits($i).$category)];
      } else {
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
}

//Close connection
mysqli_close($link);


?>
