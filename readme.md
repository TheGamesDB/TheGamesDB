TheGamesDB.net
==============

http://thegamesdb.net

This site serves as a frontend to a complete database of video games.

The site includes artwork and metadata that can be incorporated into various HTPC software and plug-ins via our API.


Getting Started
---------------

First, you will need to download a copy of the code. If you are unfamiliar with GitHub, the easiest way to do this is to click the "ZIP" button at the top of our GitHub page.

To get the site to run locally, you will need to install and configure the following components:

<dl>
  <dt>> Apache (v2.4.2 or Less Recommended)</dt>
  <dd>Additional Requirements: rewrite_module</dd>
  <dt>> MySQL (v5.5.8 or Less Recommended)</dt>
  <dd>Additional Requirements: none</dd>
  <dt>> PHP 5 (v5.3.1 or Less Recommended)</dt>
  <dd>Additional Requirements: Settings - short open tag (on) & register globals (on)... you may want to alter your php error reporting settings to "error_reporting = E_ALL" to disable coding standards warnings for some deprecated functions that currently remain in use.</dd>
</dl>

The quickest way to install and configure these components is by using XAMPP/LAMP on Linux or WAMP on Windows.

Once these components are installed you will want to extract the site code into your www or htdocs directory.

You will then need to set up the sample database. To do this use your favorite database management tool (PhpMyAdmin/Navicat) to import the "sample_db.sql" file into a new mysql database.

An admin account has been configured in the Users DB Table for you; the defualt username is "admin" and the default password is "admin".

Finally you will need to configure the basic system settings. To do this copy and rename the "config.template.php" file to "config.php". Then open up this new file in your favorite text editor, and insert your database connection settings and Base URL settings.

*Now you should be good to go! Navigate to your web-server's url (typically "http://localhost" for local web-servers such as WAMP/LAMP/XAMPP)in your favorite browser and see if it all works!*