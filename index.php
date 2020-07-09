<?php
require "includes/config.php";
// the Website Header
require "includes/ws-header.php";
?>


<?php
if( empty($_POST['submit']) ){
?>

<div class="jumbotron">
   <h1 class="display-4">Protect your links</h1>
   <p class="lead">Protect your links with a captcha and password. You can also add an expiration time.</p>
</div>


<div>
<form method="post" action="index.php">

		<div class="form-group">
			<label for="enctext" class="control-label">Links</label>
	  	<textarea class="form-control" id="enctext" name="enctext" aria-describedby="textareaHelpBlock" rows="5" maxlength="5000" required></textarea>
			<small id="textareaHelpBlock" class="form-text text-muted">
				Enter one valid link per line. No other seperators.
				max <span id="charCounter">0</span> / 5000 characters.
			</small>
	  </div>
		<hr>

    <div class="form-group">
	    <div class="custom-control custom-switch">
				<input type="checkbox" class="custom-control-input"  id="enableClicknload" name="enableClicknload" aria-describedby="enableClicknloadHelpBlock" />
				<label class="custom-control-label" for="enableClicknload">Click'n'Load</label>
				<small id="enableClicknloadHelpBlock" class="form-text text-muted">
					Adds a Click'n'Load button to import the links in <a href="https://jdownloader.org/" rel="nofollow">JDownloader</a>.
				</small>
	    </div>
	  </div>
    <hr>

		<div class="form-group">
			<label for="password" class="control-label">Password</label>
			<input type="text" class="form-control" id="password" name="password" placeholder="optional password for your link" />
	  </div>

    <?php if(CAPTCHA_ENABLED_LINK == true){ ?>
    <div class="form-group">
	    <div class="custom-control custom-switch">
				<input type="checkbox" class="custom-control-input"  id="enableCaptcha" name="enableCaptcha" aria-describedby="enableCaptchaHelpBlock" checked />
				<label class="custom-control-label" for="enableCaptcha">Captcha</label>
				<small id="enableCaptchaHelpBlock" class="form-text text-muted">
					To verify that it is a human and no robot.
				</small>
	    </div>
	  </div>
    <?php } ?>

		<div class="form-group">
			<label for="expireDate" class="control-label">Link expires</label>
			<select class="custom-select" name="expireDate" id="expireDate">
				<option value="never">never</option>
				<option value="1day">in 1 day</option>
				<option value="1week">in 1 week</option>
        <option value="2week">in 2 weeks</option>
				<option selected value="1month">in 1 month</option>
				<option value="6month">in 6 months</option>
				<option value="1year">in 1 year</option>
			</select>
	  </div>

		<hr>

    <?php if(CAPTCHA_ENABLED_CREATE == true){ ?>
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
              <img id="captcha" src="/assets/securimage/securimage_show.php" alt="CAPTCHA Image" />
              <object type="application/x-shockwave-flash" data="/assets/securimage/securimage_play.swf?audio_file=/assets/securimage/securimage_play.php&amp;bgColor1=%23fff&amp;bgColor2=%23fff&amp;iconColor=%23777&amp;borderWidth=1&amp;borderColor=%23000" width="19" height="19"> <param name="movie" value="/assets/securimage/securimage_play.swf?audio_file=/assets/securimage/securimage_play.php&amp;bgColor1=%23fff&amp;bgColor2=%23fff&amp;iconColor=%23777&amp;borderWidth=1&amp;borderColor=%23000" /> </object>
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
	      <input type="submit" name="submit" id="submit" class="btn btn-primary" value="Create Link">
	    </div>
	  </div>

</form>
</div>

<script>
$('textarea').on("input", function(){
  var currentLength = $(this).val().length;
	document.getElementById("charCounter").innerHTML = currentLength;
});
</script>


<?php
}
else {
  ?>
  <div class="jumbotron">
	   <h1 class="display-4">Your protected links</h1>
	</div>
  <?php
  // debug:
  // echo "<br>POST: "; print_r($_POST);

  // check the Captcha
  if(CAPTCHA_ENABLED_CREATE == true){
    $captchaCheckIs = false;
    if(CAPTCHA_SERVICE == "hcaptcha"){
      // hCaptcha request
      $data = array(
          'secret' => CAPTCHA_PRIVKEY,
          'response' => $_POST['h-captcha-response']
      );
      $verify = curl_init();
      curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
      curl_setopt($verify, CURLOPT_POST, true);
      curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
      curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($verify);
      $responseData = json_decode($response);
      if($responseData->success) { $captchaCheckIs = true; }
    } elseif(CAPTCHA_SERVICE == "recaptcha"){
      // reCaptcha request
      $data = array(
          'secret' => CAPTCHA_PRIVKEY,
          'response' => $_POST['h-captcha-response']
      );
      $verify = curl_init();
      curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
      curl_setopt($verify, CURLOPT_POST, true);
      curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
      curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($verify);
      $responseData = json_decode($response);
      if($responseData->success) { $captchaCheckIs = true; }
    } else{
      // Securimage
      include_once 'assets/securimage/securimage.php';
    	$securimage = new Securimage();
    	if ($securimage->check($_POST['captcha_code'])) { $captchaCheckIs = true; }
    }

    // incorrect Captcha:
    if(!$captchaCheckIs){
      echo '<div class="alert alert-danger">'.
			   'We could not verify that you are a human. The captcha was incorrect. '.
			   '<a href="index.php" class="alert-link">Reload the Form</a> and try again.'.
			   '</div>';
		  die;
    }
  }



	// split textarea per line, https://stackoverflow.com/a/3702430/5905273
	$textInput = trim($_POST['enctext']);
	$textAr = explode("\n", $textInput);
	$textAr = array_filter($textAr, 'trim');

	// foreach link
	foreach ($textAr as $inputKey => $inputLink) {
		// remove possible whitespace
		$inputLink = preg_replace('/\s+/', '', $inputLink);
		// validate the link
		if(!filter_var($inputLink, FILTER_VALIDATE_URL)) {
			echo "<p>".$inputLink.": <br>";
			echo 'This was no valid URL.';
			unset($textAr[$inputKey]);
		}
	} // end. foreach



  // build the data to store in the database:
  $dataLinks = json_encode($textAr);
  // encryption key: alphanumeric characters of length = bytes*2 = 8*2 = 16
  $dataKey = openssl_random_pseudo_bytes(8);
  // debug:
  // echo "<br>data: "; print_r($dataLinks); echo "<br>id: "; print_r($dataId); echo "<br>key: "; print_r(bin2hex($dataKey));

  // encryption
  $cipher = "aes-256-ctr";
	if (in_array($cipher, openssl_get_cipher_methods())) {
	    $ivlen = openssl_cipher_iv_length($cipher);
	    $iv = openssl_random_pseudo_bytes($ivlen);
	    $ciphertext = openssl_encrypt($dataLinks, $cipher, $dataKey, $options=0, $iv);
      // debug:
      // echo "<br>ciphertext: "; print_r($ciphertext); echo "<br>iv: "; print_r(bin2hex($iv));
	    //$original_plaintext = openssl_decrypt($ciphertext, $cipher, $dataKey, $options=0, $iv, $tag);
	    //echo $original_plaintext."\n";
	}
	else {
		echo '<div class="alert alert-danger">'.
			'Encryption mode not available.'.
			'</div>';
		die;
	}


  // password as sha256 hash stored in database
  // the hash includes $dataKey and $iv
  if(!empty($_POST['password'])){
    $passwordHash = hash("sha256", $dataKey.$iv.$_POST['password']);
  }

  // enable captcha
  if(CAPTCHA_ENABLED_LINK == true && $_POST['enableCaptcha'] == "on"){
    $enableCaptcha = 1;
  } else{
    $enableCaptcha = 0;
  }

  // enable click'n'load
  if($_POST['enableClicknload'] == "on"){
    $enableClicknload = 1;
  } else{
    $enableClicknload = 0;
  }


  // expire date:
  switch ($_POST['expireDate']){
  	case "1day":
  		$expireDate = time() + (24 * 60 * 60);
  		break;
  	case "1week":
      $expireDate = time() + (7 * 24 * 60 * 60);
  		break;
  	case "2week":
      $expireDate = time() + (14 * 24 * 60 * 60);
  		break;
  	case "1month":
      $expireDate = time() + (31 * 24 * 60 * 60);
  		break;
  	case "6month":
      $expireDate = time() + (6*30 * 24 * 60 * 60);
  		break;
  	case "1year":
      $expireDate = time() + (365 * 24 * 60 * 60);
  		break;
  	default:
      $expireDate = 0;
  }


  // insert in MySQL database: ID, ciphertext, passwordHash, enableCaptcha, expireDate
  require "includes/bdd.php";
  $dbQuery = $db->prepare("INSERT INTO `links` (
		`ID`, `ciphertext`, `passwordHash`, `enableCaptcha`, `enableClicknload`, `expireDate`
	) VALUES (
		'',   :ciphertext,  :passwordHash,  :enableCaptcha,  :enableClicknload,  :expireDate
	);");
  $dbExecData = array(
	   ":ciphertext" => $ciphertext,
	   ":passwordHash" => $passwordHash,
     ":enableCaptcha" => $enableCaptcha,
     ":enableClicknload" => $enableClicknload,
     ":expireDate" => $expireDate
  );

  if( !$dbQuery->execute($dbExecData) ){
		echo '<div class="alert alert-danger">'.
			'Error while executing the database statement.'.
			'</div>';
		die;
  }
  $pdoLastId = $db->lastInsertId();




	// output
  if(FANCY_LINKS){
    $mailURL = DEFAULT_URL."/s/".$pdoLastId."/".bin2hex($dataKey)."/".bin2hex($iv)."/";
  }else{
    $mailURL = DEFAULT_URL."/show.php?id=".$pdoLastId."&key=".bin2hex($dataKey)."&iv=".bin2hex($iv);
  }
	?>
	<h1>Your link</h1>
	<p>This is your protected link:<br> <br>
	<div class="input-group mb-3">
		<input class="form-control" type="text" value="<?php echo $mailURL; ?>" aria-describedby="copy-button" readonly >
		<div class="input-group-append">
      <script src="https://unpkg.com/clipboard@2.0.6/dist/clipboard.min.js" integrity="sha384-x6nRSkfSsKGBsvlLFHHNju+buS3zYUztVnTRz/0JKgOIk3ulS6bNce/vHOvYE2eY" crossorigin="anonymous"></script>
			<script type="text/javascript"> new ClipboardJS('.btn-copy'); </script>
	    <button class="btn btn-outline-secondary btn-copy" type="button" id="copy-button" name="copy-button" title="Copy to clipboard"
			data-clipboard-text="<?php echo $mailURL; ?>" data-toggle="tooltip" data-trigger="click" data-placement="top" data-original-title="<?php echo _("Copied!"); ?>" >copy</button>
	  </div>
	</div>
	</p>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title"><?php echo _('Share your link'); ?></h2>
		</div>
		<div class="panel-body">
			<p><?php echo _('You can share this link now on the internet.'); ?></p>
      <?php if(URL_SHORTENER_ENABLED == true){ ?>
        <p><?php echo _('Too long?'); ?> <a href="<?php echo URL_SHORTENER_URL.urlencode($mailURL); ?>" target="_blank"><?php echo _('You can shorten this URL</a>.'); ?></a></p>
      <?php } ?>
			<p><?php echo _('Share the link on');  ?></p>
        <ul>
				      <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($mailURL); ?>" target="_blank"><?php echo _('Share on Facebook'); ?></a></li>
				      <li><a href="https://twitter.com/share?url=<?php echo urlencode($mailURL); ?>&text=<?php echo urlencode(_('Link: ')); ?>" target="_blank"><?php echo _('Share on Twitter'); ?></a></li>
				      <li><a href="mailto:?to=&subject=<?php echo rawurlencode(_('Link')); ?>&body=<?php echo rawurlencode(_('Link: ') . $mailURL); ?>" target="_blank"><?php echo _('Share via email'); ?></a></li>
			  </ul>
		</div>
	</div>


	<?php
}
?>



<?php
// the Website Footer
require "includes/ws-footer.php";
?>
