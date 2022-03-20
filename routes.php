<?php

// Views
$app->get('/', ['visitor@visited'], function($req, $res) {
    $crud = new Crud;
    $users = $crud->find('users');
    if (isset($_GET['age_min'])) {
        $users = $crud->query_fetch('SELECT * FROM users WHERE age > ?', [$_GET['age_min']]);
    }

    $res->render('home', ['users' => $users]);
});

$app->get('/add-user', ['visitor@visited'], function($req, $res) {
    $res->render('add-user');
});

$app->get('/update-user/:user_id', ['visitor@visited'], function($req, $res) {
    $crud = new Crud;
    $user = $crud->findOne('users', [
        'id' => $req->params->user_id
    ]);

    $res->render('update-user', ['user' => $user]);
});

// Post Actions
$app->post('/add-user', function($req, $res) {
    $crud = new Crud;
    $user = $crud->create('users', [
        'fullname' => $_POST['fullname'],
        'email' => $_POST['email'],
        'age' => $_POST['age'],
    ]);

    $res->message('success', 'Kullanıcı başarıyla eklendi!')->redirect('/');
});

$app->post('/update-user/:user_id', function($req, $res) {
    $crud = new Crud;
    $user = $crud->update('users', [
        'fullname' => $_POST['fullname'],
        'email' => $_POST['email'],
        'age' => $_POST['age'],
    ], [
        'id' => $req->params->user_id
    ]);

    if ($user === false) return $res->message('alert', 'Kullanıcı güncellenemedi!')->redirect('/');

    $res->message('success', 'Kullanıcı başarıyla güncellendi!')->redirect('/');
});

$app->get('/delete-user/:user_id', function($req, $res) {
    $crud = new Crud;
    $status = $crud->delete('users', [
        'id' => $req->params->user_id
    ]);

    if ($status === false) return $res->message('alert', 'Kullanıcı silinemedi!')->redirect('/');

    $res->message('success', 'Kullanıcı başarıyla silindi!')->redirect('/');
});