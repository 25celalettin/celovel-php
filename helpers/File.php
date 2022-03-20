<?php

class File {
    private static $allowed_img_exts = ['png', 'jpg', 'jpeg', 'ico'];

    private $name;
    private $type;
    private $tmp_name;
    private $size;
    private $error;
    private $config;
    private $ext;

    private static $uploadErrors = array(
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the upload max filesize allowed.',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder'
    );

    function __construct($key, $config = []) {
        $this->name = $_FILES[$key]['name'];
        $this->type = $_FILES[$key]['type'];
        $this->tmp_name = $_FILES[$key]['tmp_name'];
        $this->size = $_FILES[$key]['size'];
        $this->error = $_FILES[$key]['error'];

        $ext = explode('.', $_FILES[$key]['name']);
        $this->ext = end($ext);

        $this->config = $config;

        if (isset($config['ext_array'])) {
            self::$allowed_img_exts = $config['ext_array'];
        }

        $this->is_error();
        $this->check_config();
    }

    private function is_error() {
        if ($this->error != 0) $this->error = self::$uploadErrors[$this->error];
    }

    private function check_config() {
        if (isset($config['max_size']) && $config['max_size'] < $this->size) {        
            $this->error = 'max file size: ' . $config['max_size'];
        }

        if (!in_array($this->ext, self::$allowed_img_exts) && count(self::$allowed_img_exts) > 0) {
            $this->error = 'only allowed extensions: ' . implode(' - ', self::$allowed_img_exts);
        }
    }

    public function check_error() {
        if ($this->error == 0)
            return true;
        return $this->error;
    }

    public function get_ext() {
        return $this->ext;
    }

    public function get_size_kb() {
        return $this->size / KB;
    }

    public function get_size_mb() {
        return $this->size / MB;
    }

    public function set_name($name) {
        $this->name = $name . '.' . $this->ext;
    }

    public function upload($dir) {
        $public_image_path = '/' . $dir . '/' . $this->name;
        $result = move_uploaded_file($this->tmp_name, PUBLIC_DIR . $public_image_path);
        return $result ? $public_image_path : false;
    }

}