<?php

function add() {

    $file = new LoadFile($_FILES["blog_image"]);

    if (!in_array($file->ext, LoadFile::$allowed_img_exts)) {
        echo 'yanlis dosya uzantisi';
        return;
    }

    if ($file->get_size_mb() > 5) {
        echo '5 megabayttan buyuk';
        return;
    }

    $file->set_name('eller_yaniyor');

    echo $file->upload('/img') ? 'basarili' : 'basarisiz';
        

}