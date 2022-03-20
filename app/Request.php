<?php

class Request {
    public $params = [];
    public $query = [];
    public $server = [];
    public $body = [];
    public $current_path;

    public function __construct($obj) {
        $this->params = (object) $obj['params'] ?? [];
        $this->query = (object) $_GET;
        $this->server = (object) $_SERVER;
        $this->body = (object) $obj['body'] ?? [];
        $this->current_path = $obj['current_path'] ?? '';
    }

}