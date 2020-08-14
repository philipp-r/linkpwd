<?php
require_once "includes/config.php";
require_once "includes/linkpwd.class.php";

// output as JSON
header('Content-type: application/json');

// API enabled
if( API_ENABLED == false ){
  header($_SERVER["SERVER_PROTOCOL"].' 423 Locked');
  $returnValues = array(
    "status" => 423,
    "errormsg" => "API is disabled"
  );
  exit( json_encode($returnValues) );
}



// API key
if( $_POST['username'] == "username" || $_POST['password'] == "password" ||
  API_KEYS[$_POST['username']] != API_KEYS[$_POST['password']] ){
  header($_SERVER["SERVER_PROTOCOL"].' 401 Unauthorized');
  $returnValues = array(
    "status" => 401,
    "errormsg" => "invalid API username or password"
  );
  exit( json_encode($returnValues) );
}
