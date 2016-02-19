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

define('AUTH_KEY',         'iN>dNqeh-TmoUZf|; A=[S}m fnZpPh?5E6E9j`POn%g@FLuf-PiD(-K$#XzRYX|');
define('SECURE_AUTH_KEY',  'AzS1BJM>&#4q3VHegDSxQxx#0,N95q-+k.=MZpH>T,|tsxmc~28hW#nwjp`Er]/$');
define('LOGGED_IN_KEY',    'ic[_LSMqs%OX5w|)0l.50pqw~BT$3T*!(m<8hij1:o 3+rNVNr;[]V*wtl.sWqUV');
define('NONCE_KEY',        'ez%pL6|Mq;:pQ<yRgrv5lco?eexB-R#f2,R{$&;Wv> [L|S7145}**7wuNkq#5;~');
define('AUTH_SALT',        '~SM 2<n[>PpPem#RvRWMX{I07tR36nq`B nv<9E9gf}J]dR#fx{[PMct,mmJ^X0{');
define('SECURE_AUTH_SALT', '$.wLbt0-3{d|2f@W7Z9hfI;M~`CYBM7$G|)/bRtke3Fq? O]q1X=1B?~+z84yC]`');
define('LOGGED_IN_SALT',   '.4Psuz&l]HoJ5h@{*PSf={Cv.kz~ppT*CkB|X8wx}N|+PUvu.4/FnvIJ|?,#Wap9');
define('NONCE_SALT',       '0*hsn0Z7wlrCs=.ciM&W;7p+r]U4P:!QQZ K-4ZanMYuagwT)GFA<5,kA$-RNRUF');

define('WP_HOME','http://192.168.1.9/~jjpro/wp_clean');
define('WP_SITEURL','http://192.168.1.9/~jjpro/wp_clean');

$table_prefix = 'wp_';





/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
