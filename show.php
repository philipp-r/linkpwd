<?php
require_once "includes/config.php";
require_once "includes/linkpwd.class.php";



// validate the link
$isValidLink = validateLink($_GET['id'], $_GET['key'], $_GET['iv']);
if( $isValidLink[0] == false ){
  header($_SERVER["SERVER_PROTOCOL"].' 404 Not Found');
  require "includes/ws-header.php";
  echo '<div class="alert alert-danger">'.$isValidLink[1].' <a href="'.DEFAULT_URL.'" class="alert-link">Go to the homepage</a>.</div>';
  die;
}


// get data from database
$dbD = getLinkData($_GET['id']);


// the Website Header
require "includes/ws-header.php";
?>
<h1 class="display-4">Protected links</h1>
<?php echo HEADER_HTMLCODE; ?>
<?php


$showLinks = true;

// check password and captcha if a password is set or captcha is enabled
if( !empty($dbD['passwordHash']) || ( $dbD['enableCaptcha'] == 1 && CAPTCHA_ENABLED_LINK ) ){
  $showLinks = false;
  // show the password/captcha form
  if( empty($_POST['submit']) ){
  ?>
  <div>
  <form method="post" action="">
      <?php if( !empty($dbD['passwordHash']) ){ ?>
      <div class="form-group">
  			<label for="password" class="control-label">Password</label>
  			<input type="password" class="form-control" id="password" name="password" placeholder="" />
  	  </div>
      <?php } ?>

      <?php if($dbD['enableCaptcha'] == 1 && CAPTCHA_ENABLED_LINK){ ?>
      <div class="form-group">
          <?php if(CAPTCHA_SERVICE == "hcaptcha"){ ?>
              <script src="https://hcaptcha.com/1/api.js" async defer></script>
            	<div class="h-captcha" data-sitekey="<?php echo CAPTCHA_SITEKEY; ?>"></div>
              <small id="captcha_code" class="form-text text-muted">
              	<?php echo _('Please click this box and follow possible instructions to verify that you are a human. '); ?>
              </small>
          <?php } elseif(CAPTCHA_SERVICE == "recaptcha"){ ?>
              <script src="https://www.google.com/recaptcha/api.js" async defer></script>
              <div class="g-recaptcha" data-sitekey="<?php echo CAPTCHA_SITEKEY; ?>"></div>
              <small id="captcha_code" class="form-text text-muted">
                <?php echo _('Please click this box and follow possible instructions to verify that you are a human. '); ?>
              </small>
          <?php } else{ ?>
              <span class="help-block">
                <img id="captcha" src="<?php echo DEFAULT_URL; ?>/vendor/dapphp/securimage/securimage_show.php" alt="CAPTCHA Image" />
                <object type="application/x-shockwave-flash" data="<?php echo DEFAULT_URL; ?>/vendor/dapphp/securimage/securimage_play.swf?audio_file=<?php echo DEFAULT_URL; ?>/vendor/dapphp/securimage/securimage_play.php&amp;bgColor1=%23fff&amp;bgColor2=%23fff&amp;iconColor=%23777&amp;borderWidth=1&amp;borderColor=%23000" width="19" height="19"> <param name="movie" value="<?php echo DEFAULT_URL; ?>/vendor/dapphp/securimage/securimage_play.swf?audio_file=<?php echo DEFAULT_URL; ?>/vendor/dapphp/securimage/securimage_play.php&amp;bgColor1=%23fff&amp;bgColor2=%23fff&amp;iconColor=%23777&amp;borderWidth=1&amp;borderColor=%23000" /> </object>
              </span>
              <input type="text" class="form-control" id="captcha_code" name="captcha_code" required />
              <small id="captcha_codeHelp" class="form-text text-muted">
                <?php echo _('Please enter the letters (not case sensitive) and numbers above to verify that you are a human. '); ?>
              </small>
          <?php } ?>
  	  </div>
      <?php } ?>

  	  <div class="form-group">
  	    <div class="col-sm-offset-2 col-sm-10">
  	      <input type="submit" name="submit" id="submit" class="btn btn-primary" value="Show me">
  	    </div>
  	  </div>
  </form>
  </div>
  <?php }
  // if the form was submitted
  else{
    // validate captcha, if enabled
    if($dbD['enableCaptcha'] == 1 && CAPTCHA_ENABLED_LINK){
        $captchaCheckIs = false;
        if(CAPTCHA_SERVICE == "hcaptcha"){
          $responseData = checkReCaptcha("https://hcaptcha.com/siteverify", CAPTCHA_PRIVKEY, $_POST['h-captcha-response']);
          if($responseData->success) {
            $captchaCheckIs = true;
          }
        } elseif(CAPTCHA_SERVICE == "recaptcha"){
          $responseData = checkReCaptcha("https://www.google.com/recaptcha/api/siteverify", CAPTCHA_PRIVKEY, $_POST['g-recaptcha-response']);
          if($responseData->success) {
            $captchaCheckIs = true;
          }
        } else{
          // Securimage
          include_once 'vendor/dapphp/securimage/securimage.php';
        	$securimage = new Securimage();
        	if ($securimage->check($_POST['captcha_code'])) { $captchaCheckIs = true; }
        }
        // incorrect Captcha:
        if(!$captchaCheckIs){
          echo '<div class="alert alert-danger">'.
		    	   'We could not verify that you are a human. The captcha was incorrect. '.
		    	   '<a href="'.$_SERVER["REQUEST_URI"].'" class="alert-link">Reload the Form</a> and try again.'.
		    	   '</div>';
		      die;
        }
    }

    // validate password, if enabled
    if( !empty($dbD['passwordHash']) ){
        $passwordSubmittedHash = hash("sha256", hex2bin($_GET['key']).hex2bin($_GET['iv']).$_POST['password']);
        if( $passwordSubmittedHash != $dbD['passwordHash'] ){
          echo '<div class="alert alert-danger">'.
		    	   'The password was incorrect or this link is invalid. '.
		    	   '<a href="'.$_SERVER["REQUEST_URI"].'" class="alert-link">Reload the Form</a> and try again.'.
		    	   '</div>';
		      die;
        }
    }
    // otherwise everything is okay
    $showLinks = true;

  } // end if form was submitted

} // end if password is set or captcha is enabled




if($showLinks == true){
  // decryption
  $dataLinks = decryptLinks($dbD['ciphertext'], $_GET['key'], $_GET['iv']);
  // foreach link
  echo "<pre><code>";
  foreach ($dataLinks as $dataLink) {
    // remove whitespace
    $dataLink = htmlspecialchars(preg_replace('/\s+/', '', $dataLink));
    // echo
    echo "<a href='".$dataLink."' target='_blank'>".$dataLink."</a><br>";
  } // end. foreach
  echo "</code></pre>";


  if($dbD['enableClicknload'] == 1){ ?>
    <div>
    <form action="http://127.0.0.1:9666/flash/add" target="hidden" method="POST">
      <input type="hidden" name="passwords" value="">
      <input type="hidden" name="source" value="<?php echo $_SERVER["REQUEST_URI"]; ?>">
      <input type="hidden" name="urls" value="<?php foreach($dataLinks as $dataLink){ $dataLink = htmlspecialchars(preg_replace('/\s+/', '', $dataLink)); echo $dataLink."\r\n"; } ?>">
      <input type="submit" class="btn btn-success btn-sm" name="submit" value="Add to JDownloader">
    </form>
    <small>
      <script language="javascript">
        var jdownloader=false;
      </script>
      <script language="javascript" src="http://127.0.0.1:9666/jdcheck.js"></script>
      <script language="javascript">
        if(!jdownloader){
          document.write("This works only if JDownloader is running!");
        }
      </script>
    </small>
    </div>
  <?php
  }

} // end. show links


// the Website Footer
require "includes/ws-footer.php";
?>
