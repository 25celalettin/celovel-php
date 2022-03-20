<?php

function visited($req, $res) {
    $crud = new Crud;

    $crud->create('visitors', [
        'date' => time(),
        'ip' => $req->server->REMOTE_ADDR,
        'url' => $req->current_path
    ]);
    
    return true;
}