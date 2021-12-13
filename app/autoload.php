<?php

// required code files
$core_files = [
    'env.php',
    'CustomError.php',
    'TemplateEngine.php',
    'Model.php',
    'Route.php',
];
foreach ($core_files as $file) {
    require_once $file;
}

// optional helper files
$helper_files = [
    'load_file.php'
];
foreach ($helper_files as $file) {
    require_once '../helpers/' . $file;
}
