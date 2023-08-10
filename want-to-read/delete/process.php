<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//読みたい本id取得、取得できなければリダイレクト
$id = Session::getUnconfirmedData(Common::URL_WANT_TO_READ_DELETE, Input::WANT_TO_READ_BOOK_ID);

if($id == '') {
    Common::topRedirect();
}

//セッションから不要なデータ削除
Session::deleteFormData();

//ワンタイムトークン照合
Session::verifyToken();

try {
    //データベースから削除
    $db = new WantToReadBooks();

    $db->deleteWantToReadBook($id);

    //読みたい本一覧にリダイレクト
    Common::redirect(Common::URL_WANT_TO_READ);
    
} catch (Exception $e) {
    Common::errorRedirect();
}