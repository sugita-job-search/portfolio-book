<?php
//セッションスタート
session_start();
session_regenerate_id();

//クラスの自動読み込み
spl_autoload_register(function ($class) {

    //クラスファイルが存在する可能性があるフォルダ
    $folders = [
        'util',
        'db',
        'form'
    ];

    //区切り文字の\を/に変換した現在のディレクトリ
    $dir = str_replace('\\', '/', __DIR__);

    $is_read = false;

    foreach ($folders as $folder) {
        $file = $dir . '/class/'. $folder. '/'. $class. '.php';
        if (is_readable($file)) {
            require($file);
            $is_read = true;
            break;
        }
    }

    if ($is_read == false) {
        echo 'エラー';
        exit;
    }

});
