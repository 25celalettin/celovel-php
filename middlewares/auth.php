<?php
function is_loggedin($req, $res) {
    if (isset($_SESSION['user']))
        return true;
    return $res->redirect('/login');
}

function is_not_loggedin($req, $res) {
    if (isset($_SESSION['user']))
        $res->redirect('/');
    return true;
}

function is_admin($req, $res) {
    if ($_SESSION['user']->role == 'admin' || $_SESSION['user']->role == 'superadmin')
        return true;
    $res->redirect('/');
}

function is_superadmin($req, $res) {
    if ($_SESSION['user']->role == 'superadmin')
        return true;
    $res->redirect('/');
}

function api_auth() {
    // api auth with bearer token
    return true;
}