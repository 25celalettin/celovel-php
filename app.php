<?php
ob_start();
session_start();

require_once 'app/autoload.php';

$router = new Router();

function view_engine($view, $data = null) {
    $returnObj = ['template_engine' => true, 'view' => $view];
    if ($data) {
        $returnObj['data'] = $data;
    }
    return $returnObj;
}

require_once 'routes.php';