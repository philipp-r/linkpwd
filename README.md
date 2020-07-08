# linkpwd

This is a link protector working with MySQL database in PHP.

Users can enter a bunch of links (eg download links) and will get a link to share.
The link can be protected with a password and/or captcha and can be auto deleted after specified time.
Data is encrypted to prevent liability for any stored links.

Self hosted alternative to services like linkcrypt.ws or keeplinks.org.

## Installation

1. Download latest version from [GitHub](https://github.com/philipp-r/linkpwd)
2. Copy `includes/config.example.php` to `includes/config.php`
3. Edit `includes/config.php` with your configuration (only the MySQL database connection and DEFAULT_URL are required)
4. Set up your MySQL database with the `database.sql` file
5. You can set up a cronjob that calls the `cron.php` file daily to remove old data from the database


### Credit :

linkpwd developed by [philipp-r](https://github.com/philipp-r/linkpwd).
License: see `LICENSE.md` file
