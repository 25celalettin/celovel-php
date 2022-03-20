<?php
ob_start();
session_start();

require_once 'app/boot.php';

$app = new App();
require_once 'routes.php';