<?php
require_once "includes/config.php";
require_once "includes/linkpwd.class.php";

header('Content-Type: image/svg+xml');
header('Cache-Control: public, max-age=345600');
header($_SERVER["SERVER_PROTOCOL"].' 200 OK');


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



// BADGES
require 'vendor/autoload.php';
use PUGX\Poser\Render\SvgRender;
use PUGX\Poser\Poser;
$render = new SvgRender();
$poser = new Poser(array($render));




// validate the link
$isValidLink = validateLink($_GET['id'], $_GET['key'], $_GET['iv']);
if( $isValidLink[0] == false ){
  echo $poser->generate('error', 'invalid link', 'lightgray', 'plastic');
  die;
}



// get data from database
$dbD = getLinkData($_GET['id']);

// decryption
$dataLinks = decryptLinks($dbD['ciphertext'], $_GET['key'], $_GET['iv']);


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
