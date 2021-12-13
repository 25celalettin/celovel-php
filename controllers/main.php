<?php

function home () {
    $model = new Model();
    $model->connect_db();

    $blogs = $model->query_fetch('select * from blog');
    
    $data = ['name' => 'Celalettin', 'title' => 'maraba abi', 'role' => 'admin', 'todos' => ['todo1', 'todo2', 'todo3']];
    return view_engine('home', $data);
}

function blogs () {
    echo 'bloglar sayfasi';
}