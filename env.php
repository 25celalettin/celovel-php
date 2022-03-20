<?php

define('PUBLIC_DIRNAME', 'public_html');
define('ROOT_DIR', __DIR__);
define('PUBLIC_DIR', ROOT_DIR . '/' . PUBLIC_DIRNAME);

// mysql db info
const DBHOST = 'localhost';
const DBUSER = 'root';
const DBPWD = '';
const DBNAME = 'celovel_test';

// Easy to use size types
define('KB', 1024);
define('MB', 1048576);
define('GB', 1073741824);
define('TB', 1099511627776);

// ssl key for encrypt data
const SSL_KEY = 'kivancahmetkaya';
const SSL_METHOD = "AES-128-ECB";