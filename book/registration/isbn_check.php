<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//セッションの不要なデータを削除
unset($_SESSION[Session::MESSAGE]);
unset($_SESSION[Session::HISTORY][Common::URL_ISBN_INPUT]);
unset($_SESSION[Session::UNCONFIRMED][Common::URL_ISBN_INPUT]);

//ワンタイムトークン照合
Session::verifyToken();

$validation = new Validation(Common::URL_ISBN_INPUT);

//サニタイズ
$str = $validation->sanitizePost(Books::ISBN);
  
//未入力のときセッションにエラーメッセージを保存してリダイレクト
if($validation->saveMessageForEmptyInput(Input::ISBN)) {
  Common::redirect(Common::URL_ISBN_INPUT);
}

//セッションに入力履歴保存
$validation->saveHistories();

//isbn-13に変換
$isbn13 = Common::validateIsbn($str);

//形式が正しくないときセッションにエラーメッセージを保存してリダイレクト
if($isbn13 == '') {
  Session::saveMessage(Common::URL_ISBN_INPUT, Input::ISBN, Validation::INVALID_ISBN_MESSAGE);
  Common::redirect(Common::URL_ISBN_INPUT);
}

//セッションunconfirmedにisbn-13を保存

try {
  //すでに登録されているISBNのときはidを取得
  $db = new Books();
  
  $id = $db->getBookIdByIsbn($isbn13);
  
  //まだ登録されていなかったときはセッションunconfirmedにisbn-13を保存してリダイレクト
  if(!$id) {
      Session::saveUnconfirmedData(Common::URL_ISBN_INPUT, Books::ISBN, $isbn13);
      Common::redirect(Common::URL_BOOK_REGISTRATION);
    }

  //登録されているときは重複登録防止ページにリダイレクト
  Common::redirect(Common::URL_ISBN_DUPLICATION. '?'. Input::BOOK_ID. '='. $id);
} catch (Exception $e) {
    Common::redirect(Common::URL_ISBN_INPUT);
}