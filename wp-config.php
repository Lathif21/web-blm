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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_blm' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '04,a+6W^9^Y31IVe9DXmtbbAb*>0K(Of!%<|fhH*+M$1>8Yup7x$S?/c-YJg5E%q' );
define( 'SECURE_AUTH_KEY',  'BGm2n3+Mdt<CKP1m+d&7sy=|ORt`QQwY`w]y:$|_ZDs[p;08~Ph]e!U=RxH47kX^' );
define( 'LOGGED_IN_KEY',    'PLz=}b)RZpwQ(!2L?0)~b,O|{]WTz]T(|//NYZ&hNl<W`NDWV[&ccz?tqhGAcIp}' );
define( 'NONCE_KEY',        'xd6j%P4um>$bIAaPh0e.8HO`m-88%X-J)~^pH@aRl Srrk5M;9>Q*UiO;+=PHl8+' );
define( 'AUTH_SALT',        'cdUrZ^ #7[N*(UvBC.$[w5hY[}j%l8O;nII91m!Yy8UJpo~8[+i;6CSEZ<m2e#{N' );
define( 'SECURE_AUTH_SALT', '~u-B/lP*qHoWGQHAvHi/<;X;S9UQbHiy&CCuMoX=X.wn#O29Z*v>#yJy/ezN )>r' );
define( 'LOGGED_IN_SALT',   ')!ZJoCfdZi4,wG3_(sTes,ukp%P9$-6qPPE )]J?:U#HQ`KyP#bixVF.,2Z21z+z' );
define( 'NONCE_SALT',       '.UhH2~ZTp^4>#tXv_wDkfO)OM3h:J-z/vHt9}@]D8RdX^S`5(Dg0A{_di$$9i?89' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
