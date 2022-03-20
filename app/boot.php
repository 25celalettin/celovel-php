<?php

// require environment variables
require_once '../env.php';

// required code files
$core_files = [
    'CustomError.php',
    'TemplateEngine.php',
    'Crud.php',
    'Request.php',
    'Response.php',
    'App.php'
];
foreach ($core_files as $file) {
    require_once $file;
}

// optional helper files
$helper_files = [
    'functions.php',
    'File.php',
    'Session.php'
];
foreach ($helper_files as $file) {
    require_once '../helpers/' . $file;
}

require_once '../init.php';