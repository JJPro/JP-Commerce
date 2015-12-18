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

define('AUTH_KEY',         'SdN;-GYg^pu] |3csq)zabrjDvpDS$MGH7V]~l(>?r_{/`~+(VAJ4V}aq0(%#]Z{');
define('SECURE_AUTH_KEY',  'mU#o<si;>PN:V-as^a.lF-of1_qs+B[62I^+C&+k?+ods?3 bWo rH.|h=*BpO8I');
define('LOGGED_IN_KEY',    '!JZIwfu93[Hte!jk+0d|tIj/<N0Z<QY/[QO=Ydw0+Xs/4o+j^^D)DE0+*P C%Tw3');
define('NONCE_KEY',        'faKFL=YuA8sK^{?oeRGFJ,Hg !Ns@ Z.fJ9A1(]2fv^|Rb6DbN<K;LT}7huF+[-R');
define('AUTH_SALT',        '@]Oc|e.4s>#|G)U(MFYW5[6-$Ykc-(-=+VeLg@I}h}S<k|y&@5z#xBiCOJAPV`_O');
define('SECURE_AUTH_SALT', 'X^>&Pi-zv4pX9Nab9o]m6|7)2 nkU95lek,R&] ( I?/-I|l|kW-V!c?bTV<Z26i');
define('LOGGED_IN_SALT',   '~d:bNTy;m./6X][Y!rCr)R?o?zL[D@hlmtxt-UuWo@GU42&Ur0d5P3$BxI<h]ozG');
define('NONCE_SALT',       'JHY9`m=%BI$f~59+fJk^!,2D|ITc)C23Q+E+F>o1Q=-h}PHv/ Ey)7bcx8etT0?e');

define('WP_DEBUG', true);

$table_prefix = 'wp_';





/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
