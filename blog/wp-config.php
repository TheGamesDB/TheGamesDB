<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', '');

/** MySQL database username */
define('DB_USER', '');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'lH`jdU.V-8q=:mrP)~mmd+>n@+Nly3E`o40oBu>JmJrU|%5<Xf_xQ;+T#X~=1o:<');
define('SECURE_AUTH_KEY',  'R+;=+{npPoU[RRv>DArd5KH^g<#n/zr%|5353K4g}TD``7.*xCujU}bR29X`grB)');
define('LOGGED_IN_KEY',    'gL_vn3BI:=$0p_a)s.6e9ckde]8{gN}nPvYkFrN`D~M~Ak}ZoWytA`XG}IGr{[}$');
define('NONCE_KEY',        'f#Zm$ym!)*r$0+dPb!ve14}%^0Z%@5i7cW&`G));rcm ApX:gln`IR3Y<p=+.-s1');
define('AUTH_SALT',        'uiU=)p!(d}:W.>O{DG+7k)!r?yR:tmSZf`E#@9,yQ>}MHk/VN,tQVB2,x:8kZ)|t');
define('SECURE_AUTH_SALT', ' qSD< !9r^QR^z-Y]32&&&v+q{$$#()r;t#9bksA^z0@}01RdBKHPCYDKKe7_#6M');
define('LOGGED_IN_SALT',   'oY/;$(),4BHrno_qY,|l/?WJG1`#Fi?;q$HPKigX/fhIKj8&1khjqTO/ByOjQrsg');
define('NONCE_SALT',       'NMc-v8D}rJ4zvAGSmaiSiRtkbRm./YUsmNadO2._$uh^(_$wqVsw:p)&#{Rkk>}a');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'blog_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
