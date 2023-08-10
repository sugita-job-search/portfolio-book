<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//エラーメッセージと入力履歴を削除
Session::deleteMessageAndHistory();

//ワンタイムトークン照合
Session::verifyToken();

//登録に使うデータを取得、なかったらリダイレクト
$isbn_array = Session::getUnconfirmedDataArray(Common::URL_ISBN_INPUT);
$book_array = Session::getUnconfirmedDataArray(Common::URL_BOOK_REGISTRATION);

if($isbn_array == [] || $book_array == []) {
    Common::redirect(Common::URL_BOOK_REGISTRATION);
}

//セッションから不要なデータ削除
Session::deleteFormData();

//データを一つの配列にする
$data = $isbn_array + $book_array;

try {
    //isbnが登録済みのときリダイレクト
    $db = new Books();
    if($db->getBookIdByIsbn($data[Books::ISBN]) !== false) {
        Common::redirect(Common::URL_ISBN_DUPLICATION);
    }
    
    //ファイルがアップロードされたときは名前をつけて保存
    $file_name ='';
    if(isset($data[Session::FILE])) {
        $type = $data[Session::FILE_TYPE];
        if($type == 'image/png') {
            $ex = '.png';
        } else {
            $ex = '.jpg';
        }
        
        $date = new DateTime();
        $file_name = md5($date->format('c')). $ex;
        
        file_put_contents('../../img/'. $file_name, $data[Session::FILE]);
    }

    //画像の名前を配列に追加
    $data[Books::IMAGE] = $file_name;
    
    //データベースに登録
    $db->insertBook($data);


    //データベースから本のidを取得してセッションに保存
    $id = $db->getBookIdByIsbn($data[Books::ISBN]);
    Session::saveUnconfirmedData(Common::URL_BOOK_REGISTRATION, Books::ID, $id);

    Common::redirect('./complete.php');

} catch (Exception $e) {
    Common::redirect(Common::errorRedirect());
}