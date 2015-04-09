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
  <dt>> Apache (v2.4.2 or Recommended)</dt>
  <dd>Additional Requirements: rewrite_module</dd>
  <dt>> MySQL (v5.5.8 or Recommended)</dt>
  <dd>Additional Requirements: fulltext minimum word length should be set to 1 for searching game titles that include a single number in the title.</dd>
  <dt>> PHP 5 (v5.3 or v5.4 Recommended)</dt>
  <dd>Additional Requirements: Settings - short open tag (on) - Please Note: as of 23/09/2013 "register globals" is no longer required to be enabled... since PHP 5.4 has removed support for this, we had to introduce a Shiv in the code. As a result, you may want to alter your php error reporting settings to not display errors, as several uninitialized variables currently remain in use.</dd>
</dl>

The quickest way to install and configure these components is by using XAMPP/LAMP on Linux, MAMP on Mac, or XAMPP/WAMP on Windows. You can also use vagrant, as described below.

Once these components are installed you will want to extract the site code into your www or httpdocs directory.

The next step is to create a blank database and then configure the basic system settings. To do this copy and rename the "config.template.php" file to "config.php". Then open up this new file in your favorite text editor, and insert your database connection settings and Base URL settings. Please note that the base url you enter should not contain a trailing slash.

The easiest way to get sample data for this system is to use our TGDB Development Pack, you could also use the "sample_db.sql" file that is in the root of our GitHub repository, however we cannot guarantee that this file is up to date and is has no sample data to work with, just a blank database schema for posterity.

The TGDB Development Pack:
--------------------------

> Where do I get it?

You can download the TGDB Dev Pack from the following link: http://thegamesdb.net/tgdb-dev-pack.zip

*It is roughly 40MB so be sure to do it on a connection that doesn't charge you per MB!*

> What is it?

The TGDB Dev Pack is a resource pack that is intended to aid the coding community to contribute to TheGamesDB.net's source code.
The pack provides you with a small set of sample data and imagery which can be used as a test bed when coding for this project.
Two users have already been created for you. There is an administrator account (username: "admin"), and a standard user account (username: "user")... The password for either of these users is very simply "password".
To assist you in finding useful games, any games that have associated art available have been added as "favourite" games for both of these users.

> How do I install it?

Simply overwrite the "banners" folder that came with the original source code that you should have already grabbed, using the one included in this pack as a replacement.

Finally, import the "tgdb-dev[dd-mm-yyyy].sql" file into your pre-configured MySQL database using your tool of choice (PhpMyAdmin, Navicat, MySQL Workbench, MySQL Console).

That's It!
----------

*Now you should be good to go! Navigate to your web-server's url (typically "http://localhost" for local web-servers such as WAMP/LAMP/MAMP/XAMPP)in your favorite browser and see if it all works!*

Vagrant
-------

There is a "Vagrantfile" in this repository that can be used to bring up a VirtualBox test VM with the site and the TGDB development pack. It basically follows the instructions provided above inside a base Ubuntu 14.04 installation. Just issue a "vagrant up" and access http://localhost:8888/

