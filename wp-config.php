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

define('AUTH_KEY',         '9^D=&<>7tv*k[{|+(c~Z?z|rL>+k|BR%<yTA+7!=&losW<Ig`Evj?G&O87^hhc|d');
define('SECURE_AUTH_KEY',  '{><GX|g[ 4#R,gf%F#;>>^> |?-tVA}%|E)2P{!-`+2z?!@ [jDOWtv+M]puW]0l');
define('LOGGED_IN_KEY',    'Gy2`vBZ77mb8oe^^LTyGkF)-B,J]oJM!3PmVR|U;5.Hy(d>E;sQ3-X.zoT:f4EC4');
define('NONCE_KEY',        'AM*o}SHeKT#&j>[YN<zV:5M8-Cz`!QBmS?.}J-x[~O@^^V7<.w^#|Wh0p|A@ #r-');
define('AUTH_SALT',        'P9HshL#Nl+C#(JZ+@[2f@J_=si@gMwh?)dZ[X%-+6bKi-9|q,J@bLW 2&zMc*Aq-');
define('SECURE_AUTH_SALT', 'dH>+m7b=T7l9&TY+9UH_#|B)H23F&rB)u;?{/lSA2x=FK]>EDOk-njql3HPbUK8F');
define('LOGGED_IN_SALT',   '!Am6p*+Fkd@Y[|4()baw6a88q9aU{@_+MVf7VcGL-sk9.Axb%LBss2D&#i]~1`7h');
define('NONCE_SALT',       'Hup+-(kG%g97c2SOWickH2.o2Z Z=rNxf+Lu3OP-n!n)yPPg-{QvFIt!:cO:h3hQ');


$table_prefix = 'wp_';





/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
