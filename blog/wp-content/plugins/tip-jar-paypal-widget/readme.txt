=== Plugin Name ===
Contributors: rsadwick
Donate link: http://ryan.sadwick.com
Tags: paypal, tip jar, donate
Requires at least: 2.8
Tested up to: 3.1.2
Stable tag: 1.1

This widget allows you to create Paypal buttons that can be used for donations and payments.

== Description ==

Need a donate button on your website or blog?  This simple widget allows you to add a custom button and Paypal id.

Features:

*   Give the widget a display title and description.
*   Use any Paypal button (donate, add to cart, etc).
*   Links directly to your Paypal account button library: use existing buttons or create new buttons.


== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `tip-jar.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go into your widgets section and customize the Tip Jar widget.
4. Go into Paypal and create a button or load a button.  
5. Use the code from that you receive from Paypal in the "PayPal Code" field of the Tip Jar widget.

== Frequently Asked Questions ==

= Where can I find my Paypal button code? =

When you login to Paypal, go to Merchant Services - > Donate (or another button type).  Create the button and save it.  Then copy / paste the code into the Paypal Widget.  
If you have already created a button:
Go to Merchant Services - > My Saved Buttons - > View Code.


== Screenshots ==

1. The widget in the Wordpress Admin.
2. How the widget may look on your Wordpress site.
3. You can choose any type of button on Paypal and have it display on your Wordpress site.

== Changelog ==


= 1.1 =.
* Changed the way Paypal buttons are implemented:  you can now simply copy/paste the code from Paypal instead of using the Paypal button ID.

== Upgrade Notice ==
* Since we've changed the way the widget creates the Paypal button, you will need to go to the Tip Jar widget and insert the Paypal button code instead of the button ID.
* We have also removed the Button URL field.  You can edit the button's graphic within Paypal.
= 1.0 =.
* Release Version

== Upgrade Notice ==

= 1.0 =
Release version



`<?php code(); // goes in backticks ?>`