<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$title = $codename = "";
$title_err = $codename_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate title
    $input_title = trim($_POST["title"]);
    if(empty($input_title)){
        $title_err = "Please enter a title.";
    } else{
        $title = $input_title;
    }

    // Validate codename.
    $input_codename = trim($_POST["codename"]);
    if(empty($input_codename)){
        $codename_err = "Please enter a codename.";
    } else if ( preg_match('/\s/', $input_codename) ){
        $codename_err = "The codename must not contain spaces";
    } else{
        $codename = $input_codename;
    }



    // Check input errors before inserting in database
    if(empty($title_err) && empty($codename_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO categories (title, codename) VALUES (?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_title, $param_codename);

            // Set parameters
            $param_title = $title;
            $param_codename = $codename;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add new intention rounding criteria</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Create Record</h2>
                    </div>
                    <p>Please read the following rules carefully to add a new category to the intention rounding criteria otherwise the database will break.</p>
                    <br/> the 'Title' is a somewhat detailed description of the intention rounding criteria that is being measured. This 'Title' can be a sentence with spaces and is what will appear as the colum headers on the main input page. Max 255 characters.<br/>
                    <br/> the 'codename' has to be a shortened version of the Title for use in the backend. IT MUST NOT INCLUDE SPACES OR PUNCTUATION CHARACTERS. <br/>
                    <br/> for example the intention rounding criteria to check the lines sites are correctly documented has a Title 'Line sites:accurate, dressing, time and date', and a codename 'linesite'. <br/> <br/>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($title_err)) ? 'has-error' : ''; ?>">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo $title; ?>">
                            <span class="help-block"><?php echo $title_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($codename_err)) ? 'has-error' : ''; ?>">
                            <label>codename</label>
                            <textarea name="codename" class="form-control"><?php echo $codename; ?></textarea>
                            <span class="help-block"><?php echo $codename_err;?></span>
                        </div>

                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
