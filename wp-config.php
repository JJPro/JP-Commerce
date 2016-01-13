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

define('AUTH_KEY',         'x!Ys 6$^HQ4MSV=X+KB*qni1;a=+{Bi4}i_DY&O?tik]AfI|EIry())|bxbK4J<4');
define('SECURE_AUTH_KEY',  'N+:qqpT#1/w.t;aq-}7aOWIR/lLjFFj<mn]7L{[D3VCTJi!R;_([pej[}&VO>z9;');
define('LOGGED_IN_KEY',    '{QL:qXeuxLZ&=J~fCI8]T<gMOxVv@}54iP$Utg6[[8D(+]$-252e{viw6I%-~y-:');
define('NONCE_KEY',        'e}B HoI2EI{<<}(%TC$2$9!+9-=S[<{^]<om@vPuBo+c`[wR](IAR9E;;V/OWuy ');
define('AUTH_SALT',        '*tZCJ&}9jGj%2nfOg|u:QS68Z0fgN&#eKRkUf}>PI)ZMhGA _f6:~|+`~inn=lf<');
define('SECURE_AUTH_SALT', 'Px[KxO(53|rL]|dmHsU~XZs20t<k<EUTa+ie37-U(pC4iN33:NJ|Y^{[ZpT1|?ol');
define('LOGGED_IN_SALT',   'Bmt8xF6UQKQpMd=)7b~H+|w|lKF5tDm/(4C+0=5m_.cVhP!4*Rf%^(P}3**#+Y]L');
define('NONCE_SALT',       '8,nad`E!/_ynxU5%7YJ;xR&C0t1w|1%/IKa<a%j 6DI&c5AAYA0ThI?+OO>oE5CO');


$table_prefix = 'wp_';





/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
