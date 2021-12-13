<?php

$router->route('get', '/', 'main@home');

$router->route('get', '/blogs', ['auth@is_admin'], 'main@blogs');

$router->route('post', '/blog/add', 'blog@add');

$router->group('/admin', ['auth@is_admin'], function ($group_router) {
    $group_router->route('get', '/', function() {
        echo 'admin home page';
    });

    $group_router->route('get', '/dashboard', ['auth@is_superadmin'], function() {
        echo 'admin dashboard page';
    });

    $group_router->route('get', '/edit_user/:username', function($username) {
        echo 'edit user page, username: ' . $username;
    });
});