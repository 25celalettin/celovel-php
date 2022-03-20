<?php

class Crud {
    private $db;

    function __construct() {
        try {
            $this->db = new PDO('mysql:host='.DBHOST.';dbname='.DBNAME.';charset=utf8mb4',DBUSER,DBPWD);
        } catch (PDOException $e) {
            die('database connection error: ' . $e->getMessage());
        }
    }

    // EDIT ARGUMENTS
    private function editArgs($args, $op = ', ') {
        $args = implode($op, array_map(function ($item) {
            return $item . '=?';
        }, $args));
        return $args;
    }

    // UPLOAD FILES
    private function uploadFiles($images) {

        foreach($images as $image) {
            $file = new File($image['key']);
            $status = $file->check_error();
            if ($status !== true) return false;
            
            $file->set_name($image['name']);
            $path = $file->upload($image['path']);
            if ($path === false) return false;

            return [$image['column'] => $path];
        }

    }

    // DELETE FILES
    private function deleteFiles($table, $where, $images) {
        foreach($images as $image) {
            $row = $this->findOne($table, $where);

            unlink(PUBLIC_DIR . $row->$image);
        }
    }

    // POPULATE
    private function populate($datas, $populate_object) {
        foreach($populate_object as $populate) {
            $id_array = [];
            $column = $populate['column'];
            $foreign_column = $populate['foreign_column'];
            $join_key = $populate['join_key'];

            foreach($datas as $item) {
                array_push($id_array, $item->$column);
            }
            $id_array = array_unique($id_array);

            $txt = '';
            $dta = [];
            foreach($id_array as $id) {
                $txt .= $populate['foreign_column'] . ' =? OR ';
                array_push($dta, $id);
            };
            $txt = trim($txt);
            if (substr($txt, -2) == 'OR') {
                $txt = substr($txt, 0, -2);
            }

            $query = $this->db->prepare("SELECT * FROM {$populate['foreign_table']} WHERE $txt");
            $query->execute($dta);

            $will_join_data = $query->fetchAll(PDO::FETCH_CLASS);
            $join_data = [];

            foreach($will_join_data as $data) {
                $join_data[$data->$foreign_column] = $data;
            }

            foreach($datas as $ix => $data) {
                $datas[$ix]->$join_key = $join_data[$data->$column];
            }
            
        }
        return $datas;
    }

    // PAGINATION
    private function pagination() {
        
    }

    // Generate query text
    private function generateQueryText($where, $options) {
        $query_text = $where == '*' ? '' : 'WHERE ' . $this->editArgs(array_keys($where), ' AND ');

        if (isset($options['order'])) {
            $orderp = $options['order'][0] == '-' ? ' DESC' : ' ASC';
            $options['order'] = $options['order'][0] == '-' ? substr($options['order'], 1) : $options['order'];
            $query_text .= ' ORDER BY ' . $options['order']  . $orderp;
        }

        if (isset($options['limit'])) {
            $query_text .= ' LIMIT ' . $options['limit'];
        }

        return $query_text;
    }

    // CREATE
    public function create($table, $data, $options = []) {
        $set_text = $this->editArgs(array_keys($data));
        $query_data = array_values($data);

        $query = $this->db->prepare("INSERT INTO $table SET $set_text");
        $status = $query->execute($query_data);

        if ($status) return $this->db->lastInsertId();
        return false;
    }

    // FIND
    public function find($table, $where = '*', $options = []) {
        $query_text = $this->generateQueryText($where, $options);
        $query_data = $where == '*' ? [] : array_values($where);

        $query = $this->db->prepare("SELECT * FROM $table $query_text");
        $status = $query->execute($query_data);
        if (!$status) return false;

        $datas = $query->fetchAll(PDO::FETCH_CLASS);
        if (count($datas) == 0) return [];
        
        if (isset($options['populate'])) {
            $datas = $this->populate($datas, $options['populate']);
        }
        
        return $datas;
    }

    // FIND ONE
    public function findOne($table, $where = '*', $options = []) {
        $query_text = $this->generateQueryText($where, $options);
        $query_data = $where == '*' ? [] : array_values($where);

        $query = $this->db->prepare("SELECT * FROM $table $query_text");
        $status = $query->execute($query_data);
        if (!$status) return false;

        $datas = $query->fetchAll(PDO::FETCH_CLASS);
        if (count($datas) == 0) return null;
        
        if (isset($options['populate'])) {
            $datas = $this->populate($datas, $options['populate']);
        }

        $datas = $datas[0];
        
        return $datas;
    }

    // UPDATE
    public function update($table, $data, $where, $options = []) {
        if (isset($options['files'])) {
            $image_values = $this->uploadFiles($options['files']);
            if ($image_values === false) return false;
            $data += $image_values;
        }

        if (isset($options['delete_files'])) {
            $this->deleteFiles($table, $where, $options['delete_files']);
        }

        $update_text = $this->editArgs(array_keys($data));
        $where_text = $this->editArgs(array_keys($where), ' AND ');
        $query_data = array_merge(array_values($data), array_values($where));

        $query = $this->db->prepare("UPDATE $table SET $update_text WHERE $where_text");
        $status = $query->execute($query_data);
        if ($status) return true;
        return false;
    }

    // DELETE
    public function delete($table, $where = '*', $options = []) {
        if (isset($options['delete_files'])) {
            $this->deleteFiles($table, $where, $options['delete_files']);
        }
        
        $where_text = $where == '*' ? '' : 'WHERE ' . $this->editArgs(array_keys($where), ' AND ');
        $query_data = $where == '*' ? [] : array_values($where);
        
        $query = $this->db->prepare("DELETE FROM $table $where_text");
        $status = $query->execute($query_data);

        if ($status) {
            return true;
        }
        return false;
    }

    // Count Rows
    public function countRows($table, $where = '*', $options = []) {
        $query_text = $this->generateQueryText($where, $options);
        $query_data = $where == '*' ? [] : array_values($where);

        $query = $this->db->prepare("SELECT COUNT(*) as total FROM $table $query_text");
        $status = $query->execute($query_data);

        if ($status) {
            $datas = $query->fetch(PDO::FETCH_ASSOC);
            return $datas['total'];
        }
        return false;
    }

    // custom method for custom query
    public function query($query_string, $query_params = []) {
        $query = $this->db->prepare($query_string);
        $status = $query->execute($query_params);

        if ($status) {
            return self::$db->lastInsertId();
        }
        return false;
    }

    // custom method for custom query and fetch data
    public function query_fetch($query_string, $query_params = []) {
        $query = $this->db->prepare($query_string);
        $status = $query->execute($query_params);

        if ($status) {
            return $query->fetchAll(PDO::FETCH_CLASS);
        }
        return false;
    }

    // custom method for custom query and fetch data (for fetch single row)
    public function query_fetch_single($query_string, $query_params = []) {
        $query = $this->db->prepare($query_string);
        $status = $query->execute($query_params);

        if (!$status) return false;

        $datas = $query->fetchAll(PDO::FETCH_CLASS);
        if (count($datas) > 0) return $datas[0];
        return null;
    }

}