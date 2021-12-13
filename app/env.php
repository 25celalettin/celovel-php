<?php

define('PUBLIC_DIRNAME', 'public_html');
define('ROOT_DIR', substr(__DIR__, 0, -4));
define('PUBLIC_DIR', ROOT_DIR . '/' . PUBLIC_DIRNAME);

const DBHOST = 'localhost';
const DBUSER = 'root';
const DBPWD = '';
const DBNAME = 'blogs';

// Easy to use size types
define('KB', 1024);
define('MB', 1048576);
define('GB', 1073741824);
define('TB', 1099511627776);

// ssl encrypt key
const SSL_KEY = 'kivancinsakallariciksin';
const SSL_METHOD = "AES-128-ECB";