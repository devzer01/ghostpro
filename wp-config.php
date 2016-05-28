<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', false); //Added by WP-Cache Manager
define( 'WPCACHEHOME', '/home/ghosjrwf/public_html/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_NAME', 'ghost');

/** MySQL database username */
define('DB_USER', 'root');

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
define('AUTH_KEY',         'rdxw6vre8hzqarxctguokmofsykk3omexed5xapm5kga8av9vnvdxnvwbybavim4');
define('SECURE_AUTH_KEY',  'iywndglukvlh6xea2cav9xy11prftndrqhvekqbrz1eefb4b0yfawwcipjglpngd');
define('LOGGED_IN_KEY',    'a6ttnqap4kpu97wtc4krwvunzw5htfoebyjav2kj63wf8dhqpttscxmvsyk8nemm');
define('NONCE_KEY',        'dn92fy4xisunlukiqtlvdzkuqzi7rty5rbzxciftziwzjtdzmrwgu4j3pnjohi1c');
define('AUTH_SALT',        'kxoke38c38mk07tmbipefhzlocnjrhsyxlgl4gnngajvm4ftllk5nqpjyl5ffv9w');
define('SECURE_AUTH_SALT', 'k60du0akwxqp7vbi3urxgcvxecq13bu3zsd4cr4a9wut18rwgnfnrhqvhmhbm7dg');
define('LOGGED_IN_SALT',   'arpgk4d5bmonluiqamd32ae9jiqelz3dlwueamaw8agfgxxskb8noejlvxgwq9o2');
define('NONCE_SALT',       'gubfiq2dnlwnzyxdmo9essxtemjdkcouamaaddnnsorcdzt9kpwn12a3kvvz28nv');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wpkc_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
