<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//セッションから不要なデータ削除
Session::deleteFormData();

//ワンタイムトークン照合
Session::verifyToken();

$input = new Input();

//送られてきた本のidが1以上の自然数のとき取得、それ以外のときリダイレクト
$id = $input->filterIdPost(WantToReadBooks::BOOK_ID);

if($id == '') {
    Common::topRedirect();
}

try {
    //送られてきたidの本がbooksテーブルに登録されていないときリダイレクト
    $db = new Books();
    
    if($db->getImageByID($id) === false) {
        Common::topRedirect();
    }
    
    //送られてきたidの本をログイン中のユーザーが読みたい本にすでに登録しているときリダイレクト
    $db = new WantToReadBooks();

    if($db->isWantToReadBook($id)) {
        Common::topRedirect();
    }

    //読みたい本に登録
    $db->insertWantToReadBook($id);
    
    //HTTP_REFERERを取得してそのページにリダイレクト、取得できなければ読みたい本一覧にリダイレクト
    if(isset($_SERVER['HTTP_REFERER'])) {
        Common::redirect($_SERVER['HTTP_REFERER']);
    } else {
        Common::redirect(Common::URL_WANT_TO_READ);
    }

} catch (Exception $e) {
    Common::errorRedirect();
}