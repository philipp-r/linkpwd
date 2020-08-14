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
if( $_POST['apiuser'] == "username" || $_POST['apipass'] == "password" ||
  API_KEYS[$_POST['apiuser']] != $_POST['apipass'] ){
  header($_SERVER["SERVER_PROTOCOL"].' 401 Unauthorized');
  $returnValues = array(
    "status" => 401,
    "errormsg" => "invalid API username or password"
  );
  exit( json_encode($returnValues) );
}



// validate the link
$isValidLink = validateLink($_GET['id'], $_GET['key'], $_GET['iv']);
if( $isValidLink[0] == false ){
  header($_SERVER["SERVER_PROTOCOL"].' 404 Not Found');
  $returnValues = array(
    "status" => 404,
    "errormsg" => $isValidLink[1]
  );
  exit( json_encode($returnValues) );
}


// get data from database
$dbD = getLinkData($_GET['id']);


// check password if a password is set;  captcha not available for API
if( !empty($dbD['passwordHash']) ){
  // validate password
  $passwordSubmittedHash = hash("sha256", hex2bin($_GET['key']).hex2bin($_GET['iv']).$_GET['password']);
  if( $passwordSubmittedHash != $dbD['passwordHash'] ){
    header($_SERVER["SERVER_PROTOCOL"].' 403 Forbidden');
    $returnValues = array(
      "status" => 403,
      "errormsg" => "wrong password"
    );
    exit( json_encode($returnValues) );
  }
}



// decryption
$dataLinks = decryptLinks($dbD['ciphertext'], $_GET['key'], $_GET['iv']);

header($_SERVER["SERVER_PROTOCOL"].' 200 OK');
$returnValues = array(
  "status" => 200,
  "errormsg" => "ok",
  "links" => $dataLinks
);
exit( json_encode($returnValues) );
