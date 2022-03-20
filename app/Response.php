<?php

class Response {
    protected $redirect = false;
    protected $link;
    
    public function json($json) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($json);
        return $this;
    }

    public function send($send) {
        echo $send;
        return $this;
    }

    public function status($code) {
        http_response_code($code);
        return $this;
    }

    public function render($viewName, $data = []) {
        $template_engine = new PtEngine([
            'views' => ROOT_DIR . '/views',
            'cache' => ROOT_DIR . '/cache',
            'suffix' => 'celovel'
        ]);
        echo $template_engine->view($viewName, $data);
        return $this;
    }

    public function redirect($link = '/', $back = false) {
        if ($back) {
            $link = $_SERVER['HTTP_REFERER'] ?? $link;
        }
        header('Location: ' . $link);
        die();
    }

    public function message($alertName, $message) {
        Session::setAlert($alertName, $message);
        return $this;
    }
}