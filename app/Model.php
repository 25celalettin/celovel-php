<?php

class Model {
    private $db;

    public function connect_db() {
        try {
            $this->db = new PDO('mysql:host='.DBHOST.';dbname='.DBNAME.';charset=utf8mb4',DBUSER,DBPWD);
        }
        catch (Exception $e) {
            die("could not connect: ".$e->getMessage());
        }
    }

    public function query($query_string, $query_params = []) {
        $query = $this->db->prepare($query_string);
        $status = $query->execute($query_params);

        if ($status) {
            return true;
        }
        return false;
    }

    public function query_fetch($query_string, $query_params = []) {
        $query = $this->db->prepare($query_string);
        $status = $query->execute($query_params);

        if ($status) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

}