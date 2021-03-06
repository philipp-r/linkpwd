<?php
require_once "includes/config.php";
require_once "includes/linkpwd.class.php";

// the Website Header
require "includes/ws-header.php";




if( empty($_POST['submit']) ){
?>

<h1 class="display-4">Protect your links</h1>
<p class="lead">Protect your links with a captcha and password. You can also add an expiration time. <?php echo WEB_NAME; ?> is a link protector service.</p>

<?php echo HEADER_HTMLCODE; ?>

<div>
<form method="post" action="index.php">

		<div class="form-group">
			<label for="enctext" class="control-label h4">Links:</label>
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

    <div class="row">
		  <div class="form-group col-6">
		  	<label for="password" class="control-label">Password</label>
		  	<input type="text" class="form-control" id="password" name="password" aria-describedby="passwordHelpBlock" />
				<small id="passwordHelpBlock" class="form-text text-muted">
					Optional password for your link.
				</small>
	    </div>
		  <div class="form-group col-6">
		  	<label for="expireDate" class="control-label">Link expires</label>
		  	<select class="custom-select" name="expireDate" id="expireDate" aria-describedby="expireDateHelpBlock">
          <?php if(NEVER_EXPIRE_LINK == true){ ?><option value="never">never</option><?php } ?>
		  		<option value="1day">in 1 day</option>
		  		<option value="1week">in 1 week</option>
          <option value="2week">in 2 weeks</option>
		  		<option selected value="1month">in 1 month</option>
		  		<option value="6month">in 6 months</option>
		  		<option value="1year">in 1 year</option>
		  	</select>
				<small id="expireDateHelpBlock" class="form-text text-muted">
					The link will be deleted after this time.
				</small>
	    </div>
    </div><!-- /row -->


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
	      <input type="submit" name="submit" id="submit" class="btn btn-primary" value="Create Link">
	    </div>
	  </div>

</form>
</div>
<script src="<?php echo DEFAULT_URL; ?>/assets/js/charcounter.js"></script>


<?php
}
else {
  ?>
	<h1 class="display-4">Your protected links</h1>
  <?php
  // debug:
  // echo "<br>POST: "; print_r($_POST);

  // check the Captcha
  if(CAPTCHA_ENABLED_CREATE == true){
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
			   '<a href="index.php" class="alert-link">Reload the Form</a> and try again.'.
			   '</div>';
		  die;
    }
  }



	// split textarea per line, https://stackoverflow.com/a/3702430/5905273
	$textInput = trim($_POST['enctext']);
	$textAr = explode("\n", $textInput);
	$textAr = array_filter($textAr, 'trim');

	// validate links
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

	// encryption
	$encryptedData = encryptLinks($textAr);
	$ciphertext = $encryptedData[0];
	$dataKey = $encryptedData[1];
	$iv = $encryptedData[2];



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
  $dbQuery = $db->prepare("INSERT INTO `".MYSQL_TABLEPREFIX."links` (
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
    $badgeURL = DEFAULT_URL."/badge/".$pdoLastId."/".bin2hex($dataKey)."/".bin2hex($iv).".svg";
  }else{
    $mailURL = DEFAULT_URL."/show.php?id=".$pdoLastId."&key=".bin2hex($dataKey)."&iv=".bin2hex($iv);
    $badgeURL = DEFAULT_URL."/badge.php?id=".$pdoLastId."&key=".bin2hex($dataKey)."&iv=".bin2hex($iv);
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


  <?php if(BADGES_ENABLED == true){ ?>
  <p><a data-toggle="collapse" class="collapseLink" href="#collapseHtmlCode" aria-expanded="false" aria-controls="collapseHtmlCode">+ <?php echo _('Show me badges / icons'); ?></a></p>
  <div class="collapse" id="collapseHtmlCode">
    <div class="panel-heading">
			<h3 class="panel-title"><?php echo _('Badge / icon'); ?></h3>
		</div>
		<div class="panel-body">
      Image: <img src="<?php echo $badgeURL; ?>" />
      <pre><code><?php echo $badgeURL; ?></code></pre>
      <h5>BBCode for forums:</h5>
      <pre><code>[url=<?php echo $mailURL; ?>][img]<?php echo $badgeURL; ?>[/img][/url]</code></pre>
      <h5>HTML code:</h5>
      <pre><code>&lt;a href="<?php echo $mailURL; ?>" title="<?php echo WEB_NAME; ?>"&gt;&lt;img src="<?php echo $badgeURL; ?>" /&gt;&lt;/a&gt;</code></pre>
      <h5>Markdown:</h5>
      <pre><code>[![<?php echo WEB_NAME; ?>](<?php echo $badgeURL; ?>)](<?php echo $mailURL; ?>)</code></pre>
		</div>
  </div>
  <?php } ?>



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
