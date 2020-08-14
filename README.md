# linkpwd

[![GitHub release (latest)](https://img.shields.io/github/v/release/philipp-r/linkpwd)](https://github.com/philipp-r/linkpwd/releases/latest)
[![license](https://img.shields.io/badge/license-MIT-brightgreen)](https://github.com/philipp-r/linkpwd/blob/master/LICENSE.md)
[![demo](https://img.shields.io/badge/%20-demo-blueviolet)](https://linkpwd.3q3.de/)

[![GitHub issues](https://img.shields.io/github/issues/philipp-r/linkpwd)](https://github.com/philipp-r/linkpwd/issues)
[![GitHub pull requests](https://img.shields.io/github/issues-pr/philipp-r/linkpwd)](https://github.com/philipp-r/linkpwd/pulls)

This is a link protector written in PHP with a MySQL database.

Users can enter a bunch of links (eg download links) and will get a link to share.
The link can be protected with a password and/or captcha and can be auto deleted after specified time.
Data is encrypted to prevent liability for any stored links.

linkpwd is a self hosted alternative to services like keeplinks.org, filecrypt.cc or former share-links.biz, linkcrypt.ws.

**Don't want to run linkpwd on your own server?**
See the [list of public instances](https://github.com/philipp-r/linkpwd/wiki/Instances) if you just want to use it.



## Installation

1. Download latest version from [GitHub](https://github.com/philipp-r/linkpwd/releases)
2. Copy `includes/config.example.php` to `includes/config.php`
3. Edit `includes/config.php` with your configuration (only the MySQL database connection and DEFAULT_URL are required)
4. Set up your MySQL database with the `database.sql` file
5. You can set up a cronjob that calls the `cron.php` file daily to remove old data from the database

### Upgrading

1. Create a backup of your database and `includes/config.php` file
2. Download latest version from [GitHub](https://github.com/philipp-r/linkpwd/releases)
3. Remove all files from your webserver and upload the new files
4. Upload your old `includes/config.php` file
5. Call `upgrade.php` file in your webbrowser (if this file is available)
6. You can delete the `upgrade.php` file now



## Themes

linkpwd uses a [Bootstrap](https://getbootstrap.com/) css theme. You can get other styles for example from [Bootswatch](https://bootswatch.com/) and replace the `/assets/css/bootstrap.min.css` file.



## API

In `includes/config.php` set API_ENABLED to true and add an username and password to the API_KEYS array.

### api-create.php


### api-show.php

#### Request

Send a HTTP request to /api-show.php to retrieve links.

GET parameters:
 * `id`
 * `key`
 * `iv`
 * `password` = password for the link

required POST parameters:
 * `apiuser` = API username
 * `apipass` = API password

#### Response

The response is in JSON format:

```
{
  "status":404,
  "errormsg":"This is an invalid link."
}
```

It will give you a status code that is the same as the HTTP status code:
 * `401` = the API username or password was invalid
 * `403` = the password for the link was invalid
 * `404` = either id/key/iv parameters are malformatted or there is no data for this id in the database
 * `423` = the API is disabled in configuration
 * `200` = success

And the JSON has an error message to describe the problem.




## Donations

You can support this project with a donation via

Bitcoin: 16QMB6NXN677i3nHcD7vJPf1YkVod1ej9c

Ether: 0xe34864adf79aa63D34dceae9FF98438B46D0c815
