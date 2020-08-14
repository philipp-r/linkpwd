<?php
// Function: validateLink
// return: array ( boolean, string )
//         boolean is false if link is invalid;
//         string has error message
function validateLink( $linkId, $linkKey, $linkIv ){

  // check for valid input
  if( !filter_var($linkId, FILTER_VALIDATE_INT) ||
      !preg_match("/^[A-Za-z0-9]+$/", $linkKey) ||
      !preg_match("/^[A-Za-z0-9]+$/", $linkIv ) ){
        return array( false, "This is an invalid link." );
  }

  $dbD = getLinkData( $linkId );
  if( $dbD == false ){
    return array( false, "We found no data in our database for this link." );
  }

  // check the expire date
  if(   ( NEVER_EXPIRE_LINK == true && $dbD['expireDate'] != 0 && time() > $dbD['expireDate'] ) ||
        ( NEVER_EXPIRE_LINK == false && $dbD['expireDate'] == 0 )   ||
        ( NEVER_EXPIRE_LINK == false && time() > $dbD['expireDate'] )  ){
    return array( false, "This link already expired." );
  }

  // else everything successful
  return array( true, "Valid link." );

} // end Function: validateLink





// Function: getLinkData
// return: array ( boolean / array() )
//         boolean is false if no data was found
//         array contains data: ['ciphertext', 'passwordHash', 'enableCaptcha', 'enableClicknload', 'expireDate']
function getLinkData( $linkId ){
  require "bdd.php";
  $dbQuery = $db->prepare("SELECT * FROM `".MYSQL_TABLEPREFIX."links` WHERE `ID` = :ID");
  $dbExecData = array( ":ID" => $linkId );
  $dbQuery->execute($dbExecData);
  $dbD = $dbQuery->fetch(PDO::FETCH_ASSOC);
  // check if data exists
  if(!is_array($dbD)){
    return false;
  } else{
    return $dbD;
  }
} // end Function: getLinkData






// Function: encryptLinks
// return: array ( array, string )
function encryptLinks( $linksArray ){

  // build the data to store in the database:
  $dataLinks = json_encode($linksArray);
  // encryption key: alphanumeric characters of length = bytes*2 = 8*2 = 16
  $dataKey = openssl_random_pseudo_bytes(8);
  // echo "<br>data: "; print_r($dataLinks); echo "<br>id: "; print_r($dataId); echo "<br>key: "; print_r(bin2hex($dataKey));

  // encryption
  $cipher = "aes-256-ctr";
	if (in_array($cipher, openssl_get_cipher_methods())) {
	    $ivlen = openssl_cipher_iv_length($cipher);
	    $iv = openssl_random_pseudo_bytes($ivlen);
	    $ciphertext = openssl_encrypt($dataLinks, $cipher, $dataKey, $options=0, $iv);
      // echo "<br>ciphertext: "; print_r($ciphertext); echo "<br>iv: "; print_r(bin2hex($iv));
	    // $original_plaintext = openssl_decrypt($ciphertext, $cipher, $dataKey, $options=0, $iv, $tag);
	    // echo $original_plaintext."\n";
	}
	else {
		print 'Encryption mode not available.';
		die;
	}

  return array( $ciphertext, $dataKey, $iv );

} // end Function: encryptLinks



// Function: decryptLinks
// return: array ( )
function decryptLinks( $ciphertext, $linkKey, $linkIv ){

  $cipher = "aes-256-ctr";
  if (in_array($cipher, openssl_get_cipher_methods())) {
    $plaintext = openssl_decrypt($ciphertext, $cipher, hex2bin($linkKey), $options=0, hex2bin($linkIv));
    // echo "<br>ciphertext: "; print_r($dbD['ciphertext']); echo "<br>key: "; print_r(hex2bin($_GET['key'])); echo "<br>iv: "; print_r(hex2bin($_GET['iv']));
  }
  else {
	  print 'Encryption mode not available.';
  	die;
  }

  // build array of links:
  $dataLinks = json_decode($plaintext);
  // echo "<br>data: "; print_r($dataLinks);
  return $dataLinks;

} // end Function: decryptLinks






// Function: checkReCaptcha
// return:
function checkReCaptcha( $captchaServiceUrl, $captchaPrivatekey, $captchaResponse ){
  $data = array(
    'secret' => $captchaPrivatekey,
    'response' => $captchaResponse
  );
  $verify = curl_init();
  curl_setopt($verify, CURLOPT_URL, $captchaServiceUrl);
  curl_setopt($verify, CURLOPT_POST, true);
  curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
  curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($verify);
  $responseData = json_decode($response);
  return $responseData;
} // end Function: checkReCaptcha











// Function: curl_getheaders
// return:
// http://www.codrate.com/articles/get-headers-by-using-curl-in-php
function curl_getheaders( $url ){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_HEADER => true,
        CURLOPT_NOBODY => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $url));
    $headers = array();
    foreach (explode("\n", curl_exec($curl)) as $key => $header) {
        if (!$key) {
            $headers[] = $header;
        } else {
            $header = explode(':', $header);
            $headers[trim($header[0])] = isset($header[1]) ? trim($header[1]) : '';
        }
    }
    curl_close($curl);
    return count($headers) < 2 ? false : $headers;
}
