<?php

//Database configuration file
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'intentionrounding');

//connecting server
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

//error catch
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
