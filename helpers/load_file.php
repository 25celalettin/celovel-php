<?php

class LoadFile {
    public static $allowed_img_exts = ['png', 'jpg', 'jpeg', 'ico'];

    private $name;
    private $type;
    private $tmp_name;
    private $size;
    private $error;
    public $ext;

    private $uploadErrors = array(
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the upload max filesize allowed.',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder'
    );

    function __construct($file) {
        $this->name = $file['name'];
        $this->type = $file['type'];
        $this->tmp_name = $file['tmp_name'];
        $this->size = $file['size'];
        $this->error = $file['error'];

        $ext = explode('.', $file['name']);
        $this->ext = end($ext);
    }

    public function is_error() {
        if ($this->error == 0)
            return true;
        return $this->uploadErrors[$this->error];
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
        $result = move_uploaded_file($_FILES["blog_image"]["tmp_name"], PUBLIC_DIR . $dir . '/' . $this->name);
        return $result ? true : false;
    }

}