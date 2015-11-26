<?php


// ** MySQL settings ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wp_clean');

/** MySQL database username */
define('DB_USER', 'jjpro');

/** MySQL database password */
define('DB_PASSWORD', 'Lu.ji1eip');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('AUTH_KEY',         '9SRIL;OMmdqaEf6-=%0gX|qj6.MFKmcS}arzf)K326Kju5y_]NkfOdh V`uud[8b');
define('SECURE_AUTH_KEY',  'tg+/%SI:m/U]!.iM-C<DS{sQXp_z#.-_! u3h04DF+A-g-LpBC21hLFiK_3fjsA6');
define('LOGGED_IN_KEY',    '!L{/!c,TE-yP;Oh/jN|n8h3K&sD,Lr#<zL|gmY~|~Zp5+R02?CX:`E%-FX$K%}M,');
define('NONCE_KEY',        'bGc|],-TX&>-f8wulh:+3MxI-=lw9<W3[v~_^f^TXEK!_nVwkUr3^4,w3URwnvP-');
define('AUTH_SALT',        '+*EHY+eRI3cAJRY/?5`5`>-_-FR~Dm%7~X(8]v-D<6U.#|sw*ymHGH%wn-0%-o~>');
define('SECURE_AUTH_SALT', 'B0:q[^ep2B&c?I@j`p1k;T~2A~/-A{;m|+>iXw*bQdK?C8*!&]ze62Pe- ??puw/');
define('LOGGED_IN_SALT',   'hF@XO~i|,D{>yF_y73YP|+[3$u-6|.hv:ge*|I|G88v|IFm3@^52$eLzObX7)t`Q');
define('NONCE_SALT',       'ybj8}{xy#m&XOO%3.g|[/M*F`zyH;F]hE6+Q;O{l3_Q3-(?}-Q}kD[^jf|yWCIR|');

define('WP_DEBUG', true);

$table_prefix = 'wp_';





/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
