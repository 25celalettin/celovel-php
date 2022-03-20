<?php

class Session {

    public static function setAlert($alertName, $message) {
        $_SESSION['alert'][$alertName] = $message;
    }

    public static function getAlert($key) {
        if (isset($_SESSION['alert'][$key])) {
            $message = $_SESSION['alert'][$key];
            unset($_SESSION['alert'][$key]);
            return $message;
        }
    }

    public static function createUserSession($infos) {
        $_SESSION['user'] = $infos;
    }

    public static function destroyUserSession() {
        unset($_SESSION['user']);
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - (86400 * 30), '/');
        }
    }

    public static function getUserInfo($key) {
        return $_SESSION['user']->$key;
    }

    public static function isLoggedIn() {
        if (isset($_SESSION['user']))
            return true;
        return false;
    }

    public static function get_csrf() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            return $_SESSION['_csrf'] = md5(time() . rand(1000,9999));
        }
        return $_SESSION['_csrf'] ?? 'no';
    }

    public static function check_csrf() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            if (!isset($_POST['_csrf'])) {
                die('NO CSRF TOKEN!');
            }
            if ($_POST['_csrf'] !== Session::get_csrf()) {
                die('WRONG CSRF TOKEN!');
            }
        }
    }

    public static function remember_me($data) {
        $data = openssl_encrypt(json_encode($data), SSL_METHOD, SSL_KEY);
        setcookie('remember_me', $data, time() + (86400 * 30), "/");
    }

    public static function check_remember_me() {
        if (Session::isLoggedIn()) {
            return;
        }
        if (!isset($_COOKIE['remember_me'])) {
            return;
        }

        $data = json_decode(openssl_decrypt($_COOKIE['remember_me'], SSL_METHOD, SSL_KEY), true);

        $data['password'] = md5($data['password']);
        
        $query_string = make_query_string($data, ['glue' => ' AND ']);
        $query_params = array_values($data);

        $status = Model::query_fetch_single("SELECT * FROM users WHERE $query_string", $query_params);
        if ($status === false) return;
        Session::createUserSession($status);
    }
}