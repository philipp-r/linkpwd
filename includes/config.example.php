<?php

// MySQL database connection
//  enter information to connect to a MySQL database
//  use the SQL in the README.md file to create the tables
define('MYSQL_HOST', 'localhost');
define('MYSQL_DATABASE', 'url');
define('MYSQL_USER', 'url');
define('MYSQL_PASSWORD', '******');
define('MYSQL_TABLEPREFIX', '');


// the URL of your website:
//  without ending slash!
define('DEFAULT_URL', 'http://example.com');
// Fancy links
//  enable this to use fancy links: http://example.com/s/3/abc123/xyz456/
//  instead of normal links: http://example.com/show.php?id=3&key=abc123&iv=xyz456
//  fancy links will work if apache mod rewrite is installed even if this is set to false
define('FANCY_LINKS', true);
// the name of your website that is displayed in the header:
define('WEB_NAME', 'linkpwd');
// enable the option to never expire links
//   if you set this to false all links will be kept no longer than one year
define('NEVER_EXPIRE_LINK', true);


// link to your legal notice and privacy policy
//  if available enter your link here or set this to false to disable the link
//  it will add a link named "legal info + data protection" in the footer
define('LEGAL_INFO_LINK', false);
// link to your email address or contact info for DMCA deletion requests
//  if available enter your link here or set this to false to disable the link
//  you can use a Spamty.eu link to an email address: <https://spamty.eu/>
//  it will add a link named "contact + DMCA requests" in the footer
define('DMCA_CONTACT_LINK', false);


// Captcha settings
//  Enable the captcha when a user clicks on the link:
define('CAPTCHA_ENABLED_LINK', true);
// Enable the captcha that is required to create a new link:
define('CAPTCHA_ENABLED_CREATE', false);
// Choose a captcha service that you want to use
//  you can select "hcaptcha" for hcaptcha.com;
//  "recaptcha" for google.com/recaptcha; or
//  "php" for a local version of Securimage:
define('CAPTCHA_SERVICE', "php");
// for hcaptcha or recaptcha  enter your valid site key:
define('CAPTCHA_SITEKEY', "XXXXXXXXXX");
// for hcaptcha or recaptcha  enter your valid secret key:
define('CAPTCHA_PRIVKEY', "XXXXXXXXXX");


// URL shortener
//  Enable this to offer an URL shortener after a new link was created:
define('URL_SHORTENER_ENABLED', true);
// Enter the URL of some URL shortener service that you want to use:
//  you can use for example "https://3q3.de/?shortenUrl="
//  the URL to shorten will be appended (with urlencode)
define('URL_SHORTENER_URL', "https://3q3.de/?shortenUrl=");


// Badges
//  enable this to use badges that indicate how many links are up/down
//  they are available at: http://example.com/badge.php?id=3&key=abc123&iv=xyz456
define('BADGES_ENABLED', false);


// API
//  enable the API to post links and retrieve links
define('API_ENABLED', false);
// API keys
//  set an array of usernames and passwords to use the API
define('API_KEYS', array(
  "username": "password"
));


// header/footer
//  you can enter some HTML code that will be displayed in the header/footer area of the website
//  you can use this for example for your Google analytics code
//  header is placed below the page title
define('HEADER_HTMLCODE', '');
//  footer is before the links at the bottom
define('FOOTER_HTMLCODE', '');
