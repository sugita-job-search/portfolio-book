<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//ワンタイムトークン照合
Session::verifyToken();

//isbnを取得、入力されていないときisbn入力ページにリダイレクト
$isbn = Session::getUnconfirmedData(Common::URL_ISBN_INPUT, Books::ISBN);
if ($isbn == '') {
    Common::redirect(Common::URL_ISBN_INPUT);
}

//セッションの不要なデータを削除
unset($_SESSION[Session::UNCONFIRMED][Common::URL_BOOK_REGISTRATION]);
unset($_SESSION[Session::HISTORY][Common::URL_BOOK_REGISTRATION]);
unset($_SESSION[Session::MESSAGE]);

$validation = new Validation(Common::URL_BOOK_REGISTRATION);

//入力内容をフィルタリングして$inputsに追加
$validation->sanitizePost(Books::TITLE);
$validation->sanitizePost(Books::AUTHOR);
$validation->sanitizePost(Books::PUBLISHER);
$validation->sanitizePost(Books::YEAR);
$validation->filterMonth(Books::MONTH);
$validation->sanitizePost(Books::SERIES_TITLE);
$validation->filterNaturalNumberPost(Books::GENRE_ID);

//バリデーションエラーが起こるとエラーメッセージをセッションに保存
try {
    //書名
    //未入力または字数オーバー
    $validation->saveMessageForEmptyOrLongInput(Input::TITLE);

    //著者
    //未入力
    $validation->saveMessageForEmptyInput(Input::AUTHOR);

    //字数オーバー
    $validation->saveMessageForLongAuthor();

    //出版社
    //未入力または字数オーバー
    $validation->saveMessageForEmptyOrLongInput(Input::PUBLISHER);

    //出版年月
    //未入力または不正な値
    $validation->saveMessageForInvalidYearAndMonth();

    //シリーズ名
    //字数オーバー
    $validation->saveMessageForLongInput(Input::SERIES);

    //ジャンルid
    $validation->saveMessageForInvalidAndEmptyGenre();


    // var_dump($_SESSION[Session::MESSAGE]);

    //ファイルアップロードに失敗したときエラーメッセージ保存
    if (isset($_FILES['image']['error'])) {
        if ($_FILES['image']['error'] != 0 && $_FILES['image']['error'] != 4) {
            Session::saveMessage(Common::URL_BOOK_REGISTRATION, Input::IMAGE, Validation::FAILURE_UPLOAD_MESSAGE);
            //ファイルがアップロードされたときファイルのMIMEタイプがpngがjpegでなかったときエラーメッセージ保存
        } elseif ($_FILES['image']['error'] == 0) {
            if ($_FILES['image']['type'] != 'image/png' && $_FILES['image']['type'] != 'image/jpeg') {
                Session::saveMessage(Common::URL_BOOK_REGISTRATION, Input::IMAGE, Validation::INVALID_FILE);
            }
        }
    }

    //入力内容をセッションのhistoryに保存
    $validation->saveHistories();

    //セッションにエラーメッセージが保存されたときはリダイレクト
    if (isset($_SESSION[Session::MESSAGE])) {
        Common::redirect(Common::URL_BOOK_REGISTRATION);
    }

    //エラーメッセージがなければセッションunconfirmedに入力内容とファイルをを保存
    $validation->saveUnconfirmedInputs();
    if ($_FILES['image']['error'] == 0) {
        Session::saveUnconfirmedData(Common::URL_BOOK_REGISTRATION, Session::FILE, file_get_contents($_FILES[Books::IMAGE]['tmp_name']));
        Session::saveUnconfirmedData(Common::URL_BOOK_REGISTRATION, Session::FILE_TYPE, $_FILES[Books::IMAGE]['type']);
    }

    //確認ページにリダイレクト
    Common::redirect('./confirm.php');
} catch (Exception $e) {
    Common::errorRedirect();
}
