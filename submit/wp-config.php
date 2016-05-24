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
define('DB_NAME', 'ghosjrwf_wp811');

/** MySQL database username */
define('DB_USER', 'ghosjrwf_wp811');

/** MySQL database password */
define('DB_PASSWORD', 'H5S(PM74X@');

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
define('AUTH_KEY',         'fizltedjhtxm6udbrvcrekuax14pyjg4n8jprvntgpavz8f30dlmzmspv6obouxv');
define('SECURE_AUTH_KEY',  'edj9cautkyghcysk99axpqs8govrkuigbdknpky7sbsdwtoilyyjdqdhismfwmaw');
define('LOGGED_IN_KEY',    'f09h5qzig9xq9cpxefyysdhbwftmur5aiavgyzgvnecy2x8hq36utyi6cckijmpb');
define('NONCE_KEY',        'bo8livwg3tdauekhnzk0fgundzdncsl1lb6teeima7iysl6hnurq1dmpg2abssjo');
define('AUTH_SALT',        'oxn0w8vs7jk51pwxg7kxg7la4ikeampbycbfj7s9psmvcld0lc1vfcwxx4l9mes2');
define('SECURE_AUTH_SALT', 'xbva8a1ld8ujblzs7b16efmtiafkmbjpmamu1iroqkbjbrwhvm5w4ombinpu6znr');
define('LOGGED_IN_SALT',   'zb9uxyvhtxep6heb1hdpsaa8hxkwb6lil7djqlbuumuqqp4ofksbzylqciumgltj');
define('NONCE_SALT',       '8xc0n2sdbspypxnea4ktqjkvbgnvipcblxstumxj2bctyyeeluzbhm4rlwob294b');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wpoj_';

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
