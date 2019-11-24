<!DOCTYPE html>

<!-- Norman Pang 2019 -->

<html>

<!--
A PHP script to fetch the header titles and codenames of the different intention
rounding categories from the categories sql table in the intentionrounding database
-->
<?php
require_once "config.php";

// Attempt select query execution
$sql = "SELECT * FROM categories ORDER BY ID ASC";
if($result = mysqli_query($link, $sql)){
  if(mysqli_num_rows($result) > 0){

    $phpcategoryarray = array();

    while($row = mysqli_fetch_array($result)){
      array_push($phpcategoryarray, array($row['title'], $row['codename']));
    }

    // Free result set
    mysqli_free_result($result);
  } else{
    echo "<p class='lead'><em>No records were found.</em></p>";
  }
} else{
  echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}

// Close connection
mysqli_close($link);

?>

<!-- CSS to format table to look a bit nicer and  give alternate row shading -->
<style type="text/css">

body {
  font: 15px Arial, sans-serif;
}

table {
  border-collapse: collapse;
  width: 100%;
  table-layout: fixed;
}

th, td {
  text-align: center;
  padding: 1px;
}

tr:nth-child(even) {background-color: #f2f2f2;}

</style>
<!-- end of CSS -->

<head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1252">
  <title>intentionRounding</title>
</head>
<body>
  <h1>Intention rounding</h1>
  Notes:
  <br/>
  1. The top text boxes are required fields. As is the checkbox at the bottom before submission
  <br/>
  2. In the table, if you leave something blank and don't select an option, it will default to NA when submitted
  <br/>
  3. This will only work with a newer browser like chrome or edge (i.e. NOT old Internet Explorer)
  <br/> <br/>

  <!-- main form body -->
  <form method="post" action="savetoDB.php">
    Date: <input name="date" type="date" value="<?php echo date("Y-m-d");?>" required>
    Nurse: <input name="nurse" type="text" required>
    Consultant: <input name="consultant" type="text" required>
    <br/> <br/>

    <table id="dataTable" border="1" class="scrollable"> </table>

    <!-- main script next to generate the table and the radio buttons -->
    <script>

    //static variable for the number of beds on ITU
    //note if changed here needs changing in the php and vice versa
    //note there is code later to remove bed number 13. the variable 'numberofbeds' represents the highest bed number.
    let numberofbeds = 22;

    //function to create a new data type 'category' with it's name being the displayed text
    //and id being the value to be called upon in PHP (doesn't work with spaces in object
    //names when trying $_POST in the PHP)
    function category(name, id){
      this.name = name;
      this.id = id;
    }

    // passing the array collected form the earlier PHP script of the Categories
    // stored in the categories sql table
    let temparray = <?php echo json_encode($phpcategoryarray); ?>;

    //creating a new array of variable type category as defined above to have the
    //header title and codename separateely
    let columns = new Array();
    columns.push(new category("Bed number", "bed"));

    //loops through the temporary array pulled from the PHP script and places them in
    //a new array of variable type column for use with the rest of the html page
    for (let v=0; v<temparray.length; v++){
      columns.push(new category(temparray[v][0],temparray[v][1]));
    }

    //main function to generate the table. takes the arguments:
    //'table' is the html table object. 'headers' are the intention rounding criteria
    function generateTable(table, headers) {

      //function if the bed number is single digits to change it to a 2 digit layout
      //to standard the strings of the id of the radio buttons later to access from the PHP
      function minTwoDigits(n) {
        return (n < 10 ? '0' : '') + n;
      }

      //function to create dynamically generate radio buttons
      //id is the unique ID per cell (otherwise the radio buttons wont work independently per cell)
      //value is what is stored and returned from the html document. name is the visible text
      function makeRadioButton(name, value, text) {

        let label = document.createElement("label");
        let radio = document.createElement("input");
        radio.type = "radio";
        radio.name = name;
        radio.value = value;

        label.appendChild(radio);
        label.appendChild(document.createTextNode(text));
        return label;
      }


      //creating table headers
      let thead = table.createTHead();
      let row = thead.insertRow();
      for (let h of headers){
        let th = document.createElement("th");
        let text = document.createTextNode(h.name);
        th.appendChild(text);
        row.appendChild(th);
      }

      //creating table body. initial for loop creates each row
      //setting r as 1 to reflect real bed number rather than index number to use later
      //first for loop to create each row one by one (starting at r=1)
      for (let r=1 ; r <= numberofbeds ; r++) {

        //since there is no bed 13
        if(r!=13){

          //if the bed number is single digits to change it to a 2 digit layout
          //converting to strings as well
          let c = "";
          c = c + minTwoDigits(r);

          let row = table.insertRow();
          let bedNumber = row.insertCell();
          //putting in the bed number in the first column
          bedNumber.appendChild(document.createTextNode(r));
          //the next for loop to create each of the relevant cells with radio buttons in each row
          //b starts at 1 rather than 0 as the initial column array variable is the bed number
          for (let b=1 ; b<columns.length ; b++){
            let cell = row.insertCell();
            //creates the radio buttons with a unique id per cell. the unique cell ID is
            //the 2 digit row number + category.id
            //e.g. bed 4 line site accuracy cell value will have identity "04linesite"
            let yesRadio = makeRadioButton(c+columns[b].id, "yes", "Yes");
            cell.appendChild(yesRadio);
            let noRadio = makeRadioButton(c+columns[b].id, "no", "No");
            cell.appendChild(noRadio);
            let naRadio = makeRadioButton(c+columns[b].id, "NA", "NA");
            cell.appendChild(naRadio);
          }
        }
      }

      //Creating table footer for ease of reading. Simply duplicates headers as a footer
      //seems too difficult to create fixed table header and easily work across devices
      let tfoot = table.createTFoot();
      let footrow = tfoot.insertRow();

      for (let h of headers){
        let th = document.createElement("th");
        let text = document.createTextNode(h.name);
        th.appendChild(text);
        footrow.appendChild(th);
      }

      //end of generateTable function
    }

    //getting the table (id name 'dataTable') and putting it into a let
    let dataTable = document.getElementById("dataTable");

    //generating the table with the table id and the column array
    generateTable(dataTable, columns);

    </script>

    <br/> <br/>
    <!-- user action required before form submission -->
    Please check this box to confirm you have finished data entry before saving: <input name="confirmationbox" type="checkbox" required>

    <!-- save button -->
    <br/>
    <input type="submit" value="Submit">
    <br/> <br/> <br/> <br/>

  </form>

  Click here to export database as a downloadable .csv
  <br/>
  <form method='post' action='export.php'>
    <input type='submit' value='Export' name='Export'>
  </form>

  <br/> <br/> <br/>

  Click here to edit intention rounding Categories
  <br/>
  <form action="categorymanagement.php">
    <input type="submit" value="Go to category management" />
  </form>

  <br/> <br/> <br/> <br/>
  For any questions or to view source code please see the UHBristolDataScience GitHub page or you can contact me at normanpang1@gmail.com
  <br/>
  v1.5

</body>
</html>
