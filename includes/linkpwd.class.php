<?php
class linkpwd {


}



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
  require_once "bdd.php";
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
