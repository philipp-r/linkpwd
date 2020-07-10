<?php
require "includes/config.php";
header('Content-Type: image/svg+xml');
header('Cache-Control: public, max-age=345600');


// disable badges
if(!BADGES_ENABLED){
  ?>
  <svg xmlns="http://www.w3.org/2000/svg" width="99" height="18">
    <linearGradient id="smooth" x2="0" y2="100%">
        <stop offset="0"  stop-color="#fff" stop-opacity=".7"/>
        <stop offset=".1" stop-color="#aaa" stop-opacity=".1"/>
        <stop offset=".9" stop-color="#000" stop-opacity=".3"/>
        <stop offset="1"  stop-color="#000" stop-opacity=".5"/>
    </linearGradient>
    <rect rx="4" width="99" height="18" fill="#555"/>
    <rect rx="4" x="37" width="62" height="18" fill="#9f9f9f"/>
    <rect x="37" width="4" height="18" fill="#9f9f9f"/>
    <rect rx="4" width="99" height="18" fill="url(#smooth)"/>
    <g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="11">
        <text x="19.5" y="13" fill="#010101" fill-opacity=".3">links</text>
        <text x="19.5" y="12">links</text>
        <text x="67" y="13" fill="#010101" fill-opacity=".3">unknown</text>
        <text x="67" y="12">unknown</text>
    </g>
  </svg>
  <?php
  die;
}


// curl get headers function
require_once "includes/curl_getheaders.php";

// BADGES
require 'vendor/autoload.php';
use PUGX\Poser\Render\SvgRender;
use PUGX\Poser\Poser;
$render = new SvgRender();
$poser = new Poser(array($render));




// user passes $_GET['id'], $_GET['key'], $_GET['iv']
if( !filter_var($_GET['id'], FILTER_VALIDATE_INT) ||
    !preg_match("/^[A-Za-z0-9]+$/", $_GET['key']) ||
    !preg_match("/^[A-Za-z0-9]+$/", $_GET['iv']) ){
  echo $poser->generate('error', 'invalid link', 'lightgray', 'plastic'); die;
}


// get data from MySQL database
require "includes/bdd.php";
$dbQuery = $db->prepare("SELECT * FROM `links` WHERE `ID` = :ID");
$dbExecData = array(
	":ID" => $_GET['id']
);
$dbQuery->execute($dbExecData);
$dbD = $dbQuery->fetch(PDO::FETCH_ASSOC);
// The db data we get:
//  $dbD['ciphertext']
//  $dbD['expireDate']

if(!is_array($dbD)){
  echo $poser->generate('error', 'invalid link', 'lightgray', 'plastic'); die;
}



// check the expire date
if( $dbD['expireDate'] != 0 && time() > $dbD['expireDate'] ){
  echo $poser->generate('error', 'expired link', 'lightgray', 'plastic'); die;
}



// decryption
$cipher = "aes-256-ctr";
if (in_array($cipher, openssl_get_cipher_methods())) {
  $plaintext = openssl_decrypt($dbD['ciphertext'], $cipher, hex2bin($_GET['key']), $options=0, hex2bin($_GET['iv']));
}
else {
  echo $poser->generate('error', 'decryption failed', 'lightgray', 'plastic'); die;
}

// build array of links:
$dataLinks = json_decode($plaintext);


$validLinkCount = 0;
$totalLinkCount = count($dataLinks);
// foreach link
foreach ($dataLinks as $dataLink) {
  // remove whitespace
  $dataLink = preg_replace('/\s+/', '', $dataLink);
  // check for valid link
  if( filter_var($dataLink, FILTER_VALIDATE_URL) ){
    // check HTTP status
    $curlGetHeaders = curl_getheaders($dataLink);
    if(strpos($curlGetHeaders[0], '200') !== false){
      $validLinkCount = $validLinkCount+1;
    }
  }
} // end. foreach link



// define color
if($totalLinkCount == 0 || $validLinkCount < $totalLinkCount){
  $badgeColor = "red";
}else{
  $badgeColor = "brightgreen";
}



// output image
echo $poser->generate(WEB_NAME, $validLinkCount.'/'.$totalLinkCount.' online', $badgeColor, 'plastic'); die;
