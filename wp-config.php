<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '(,:z#6:B?FNA}oB/Ib&ecf<bb=)`PI<,JT5K*%yVd|Z#HCtuO>Ef*#36RNYk,EZz' );
define( 'SECURE_AUTH_KEY',   'q%a/e@pr_&rP-/wz(LsmDJABHhnKL|DHrmDG{%Mr{jfimRlJxgH7Z)-]`&;is,c*' );
define( 'LOGGED_IN_KEY',     '8NH+rf5PT);eK?t]07Q#n>Of};$..@hjfn7=|v&PM{C+8]A:gm}ZAZ3!Y/Tq+M=S' );
define( 'NONCE_KEY',         'D_$6P/rxxqvu1kC9j;Xy%Ra]$q:szNm[smr,gaRq]*7WUUj9 I3_56O8S&@MRrFr' );
define( 'AUTH_SALT',         'cVD)Z_Mw&`Ke4]Z eGWI$%!(F+k1I?+wsKS`hp37pdMesug>uylV:7RC V((elLl' );
define( 'SECURE_AUTH_SALT',  'zZWE@lx8]o_4L.Wg.WFY_l+>6%!7ilPY)^>X{#9HQ 9bKxVAH>h90-~$lD~x-IG]' );
define( 'LOGGED_IN_SALT',    'hz;[&g^}fH=Ae{ Wyc;UnBDZt*?1zn>y&yY#b.TgHA#hGuh-vLf>i1u[4GBnfp(Q' );
define( 'NONCE_SALT',        '7)<^=mIvnMY/zHRy_]3EJuPZNkiu!d,-THQo*FFwXt?iMQ}QSIKI! =~D48grrCO' );
define( 'WP_CACHE_KEY_SALT', 'Tp)D@RppV%Lg2nET5CoWl@j_b;?#*PRJy2fIp/J5@)YcXW5Lwz{8x7gbrMA~6}3<' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
