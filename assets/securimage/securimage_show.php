<?php

/**
 * Project:     Securimage: A PHP class for creating and managing form CAPTCHA images<br />
 * File:        securimage_show.php<br /
 *
 * modified with some randomness for better spam protection
 *
 * Copyright (c) 2018, Drew Phillips
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Any modifications to the library should be indicated clearly in the source code
 * to inform users that the changes are not a part of the original software.<br /><br />
 *
 * If you found this script useful, please take a quick moment to rate it.<br />
 * http://www.hotscripts.com/rate/49400.html  Thanks.
 *
 * @link http://www.phpcaptcha.org Securimage PHP CAPTCHA
 * @link http://www.phpcaptcha.org/latest.zip Download Latest Version
 * @link http://www.phpcaptcha.org/Securimage_Docs/ Online Documentation
 * @copyright 2018 Drew Phillips
 * @author Drew Phillips <drew@drew-phillips.com>
 * @version 3.6.8 (May 2020)
 * @package Securimage
 *
 */

// Remove the "//" from the following line for debugging problems
// error_reporting(E_ALL); ini_set('display_errors', 1);

require_once dirname(__FILE__) . '/securimage.php';

$img = new Securimage();

// You can customize the image by making changes below, some examples are included - remove the "//" to uncomment

//$img->ttf_file        = './Quiff.ttf';
//$img->captcha_type    = Securimage::SI_CAPTCHA_MATHEMATIC; // show a simple math problem instead of text
//$img->case_sensitive  = true;                              // true to use case sensitve codes - not recommended
//$img->image_height    = 90;                                // height in pixels of the image
//$img->image_width     = $img->image_height * M_E;          // a good formula for image size based on the height
//$img->perturbation    = .75;                               // 1.0 = high distortion, higher numbers = more distortion
//$img->image_bg_color  = new Securimage_Color("#0099CC");   // image background color
//$img->text_color      = new Securimage_Color("#EAEAEA");   // captcha text color
//$img->num_lines       = 8;                                 // how many lines to draw over the image
//$img->line_color      = new Securimage_Color("#0000CC");   // color of lines over the image
//$img->image_type      = SI_IMAGE_JPEG;                     // render as a jpeg image
//$img->signature_color = new Securimage_Color(rand(0, 64),
//                                             rand(64, 128),
//                                             rand(128, 255));  // random signature color

// see securimage.php for more options that can be set



/*
 * Additional spam protection added
 * $spamCount will be counted from 1 (default) up to 15 (maximum suspicion)
 * this defines the dificulty of the displayed captcha
 */
$spamCount = 1;
$spamCountMax = 15; // captcha will get very hard to solve if this is to high

/*
 *  User Agent
 */
// Source https://www.askapache.com/htaccess/blocking-bad-bots-and-scrapers-with-htaccess/
// or no User Agent set
$bots = "(Alexibot|Art-Online|asterias|BackDoorbot|Black.Hole|
BlackWidow|BlowFish|botALot|BuiltbotTough|Bullseye|BunnySlippers|Cegbfeieh|Cheesebot|
CherryPicker|ChinaClaw|CopyRightCheck|cosmos|Crescent|Custo|DISCo|DittoSpyder|DownloadsDemon|
eCatch|EirGrabber|EmailCollector|EmailSiphon|EmailWolf|EroCrawler|ExpresssWebPictures|ExtractorPro|
EyeNetIE|FlashGet|Foobot|FrontPage|GetRight|GetWeb!|Go-Ahead-Got-It|Go!Zilla|GrabNet|Grafula|
Harvest|hloader|HMView|httplib|HTTrack|humanlinks|ImagesStripper|ImagesSucker|IndysLibrary|
InfonaviRobot|InterGET|InternetsNinja|Jennybot|JetCar|JOCsWebsSpider|Kenjin.Spider|Keyword.Density|
larbin|LeechFTP|Lexibot|libWeb/clsHTTP|LinkextractorPro|LinkScan/8.1a.Unix|LinkWalker|lwp-trivial|
MasssDownloader|Mata.Hari|Microsoft.URL|MIDownstool|MIIxpc|Mister.PiX|MistersPiX|moget|
Mozilla/3.Mozilla/2.01|Mozilla.*NEWT|Navroad|NearSite|NetAnts|NetMechanic|NetSpider|NetsVampire|
NetZIP|NICErsPRO|NPbot|Octopus|Offline.Explorer|OfflinesExplorer|OfflinesNavigator|Openfind|
Pagerabber|PapasFoto|pavuk|pcBrowser|ProgramsSharewares1|ProPowerbot/2.14|ProWebWalker|ProWebWalker|
psbot/0.1|QueryN.Metasearch|ReGet|RepoMonkey|RMA|SiteSnagger|SlySearch|SmartDownload|Spankbot|spanner|
Superbot|SuperHTTP|Surfbot|suzuran|Szukacz/1.4|tAkeOut|Teleport|TeleportsPro|Telesoft|The.Intraformant|
TheNomad|TightTwatbot|Titan|toCrawl/UrlDispatcher|toCrawl/UrlDispatcher|True_Robot|turingos|
Turnitinbot/1.5|URLy.Warning|VCI|VoidEYE|WebAuto|WebBandit|WebCopier|WebEMailExtrac.*|WebEnhancer|
WebFetch|WebGosIS|Web.Image.Collector|WebsImagesCollector|WebLeacher|WebmasterWorldForumbot|
WebReaper|WebSauger|WebsiteseXtractor|Website.Quester|WebsitesQuester|Webster.Pro|WebStripper|
WebsSucker|WebWhacker|WebZip|Wget|Widow|[Ww]eb[Bb]andit|WWW-Collector-E|WWWOFFLE|
XaldonsWebSpider|Xenu's|Zeus)i";
if( empty($_SERVER['HTTP_USER_AGENT']) | preg_match($bots, $_SERVER['HTTP_USER_AGENT']) ){
	$spamCount=$spamCount+4;
}

/*
 *  no referer
 */
if( empty($_SERVER['HTTP_REFERER']) ){
	$spamCount=$spamCount+1;
}

/*
 *  Captcha load count
 */
if(!$_SESSION['captchaLoadCount']){ $_SESSION['captchaLoadCount'] = 1; }
else{ $_SESSION['captchaLoadCount']=$_SESSION['captchaLoadCount']+1; }
// the more often the captcha is loaded the harder it gets
$spamCount = $spamCount+($_SESSION['captchaLoadCount']/4);

/*
 *  Captcha settings
 */
// maximum value
if($spamCount > $spamCountMax){ $spamCount=$spamCountMax; }

// number of lines
$img->num_lines = $spamCount;
// enhance difficulty
$img->perturbation = $spamCount/10;
// Captcha length
$img->code_length = $spamCount+4;

// Image width
$img->image_width = 225;
if($img->code_length >= 7){
	$img->image_width = $img->code_length*32;
}

// default charset
$img->charset = 'ABCDEFGHKLMNPQRSTUVWXYZ23456789';
$img->case_sensitive = false;
if($spamCount >= 6){
	$img->charset = 'ABCDEFGHKLMNPQRSTUVWXYabcdefhikmnpqrstuvwxy23456789';
}

// default colors
$img->image_bg_color = new Securimage_Color("#f2f9f9");
$img->text_color     = new Securimage_Color("#292f35");
// colors
if($spamCount >= 3):
	// color scheme
	$colorScheme = rand(1,6);
	$colorHigh = rand(200,255);
	$colorLow1 = rand(0,170);
	$colorLow2 = rand(0,170);
	$colorRed = new Securimage_Color($colorHigh, $colorLow1, $colorLow2);
	$colorGreen = new Securimage_Color($colorLow2, $colorHigh, $colorLow1);
	$colorBlue = new Securimage_Color($colorLow2, $colorLow1, $colorHigh);
	// set colors accoring to colorScheme
	switch ($colorScheme){
	case 1:
		$img->image_bg_color = $colorRed;
		$img->text_color = $colorBlue;
		break;
	case 2:
		$img->image_bg_color = $colorRed;
		$img->text_color = $colorGreen;
		break;
	case 3:
		$img->image_bg_color = $colorGreen;
		$img->text_color = $colorBlue;
		break;
	case 4:
		$img->image_bg_color = $colorGreen;
		$img->text_color = $colorRed;
		break;
	case 5:
		$img->image_bg_color = $colorBlue;
		$img->text_color = $colorGreen;
		break;
	default:
		$img->image_bg_color = $colorBlue;
		$img->text_color = $colorRed;
		break;
	}
endif;
$img->line_color = $img->text_color;


// set namespace if supplied to script via HTTP GET
if (!empty($_GET['namespace'])) $img->setNamespace($_GET['namespace']);


$img->show();  // outputs the image and content headers to the browser
// alternate use:
// $img->show('/path/to/background_image.jpg');
