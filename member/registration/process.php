<?php
require_once('../../app/config.php');

//ワンタイムトークン照合
Session::verifyToken();

$validation = new Validation(Common::URL_MEMBER_REGISTRATION);

//セッションに保存されているエラーメッセージと入力内容を消去
Session::deleteFormData();

//入力内容をフィルタリングして$inputsに追加
$validation->sanitizePost(Users::NAME);
$validation->sanitizePost(Users::NICKNAME);
$validation->filterPost(Users::PASSWORD);
$validation->filterNaturalNumberPost(Users::GENRE_ID);


try {
    //バリデーションエラーが起こるとエラーメッセージをセッションに保存
    //アカウント名
    //未入力または字数オーバー
    if (!$validation->saveMessageForEmptyOrLongInput(Input::NAME)) {
        //登録済みのアカウント名
        $validation->saveMessageForDuplicateName();
    }

    //ニックネーム
    //未入力または字数オーバー
    $validation->saveMessageForEmptyOrLongInput(Input::NICKNAME);

    //パスワード
    //未入力
    if (!$validation->saveMessageForEmptyInput(Input::PASSWORD)) {
        //不正な文字種または文字数
        $validation->saveMessageForInvalidPassword();
    }

    //好きなジャンル
    $validation->saveMessageForInvalidGenreId(Input::FAVORITE_GENRE);

    
    //セッションにエラーメッセージが保存されたときはパスワード以外の入力内容をセッションに保存してリダイレクト
    if(isset($_SESSION[Session::MESSAGE])) {
        $validation->saveHistories();
        unset($_SESSION[Session::HISTORY][Common::URL_BOOK_REGISTRATION][Users::PASSWORD]);
        Common::redirect(Common::URL_MEMBER_REGISTRATION);
    }
    
    //入力内容をカラム名をキーとした配列として取得
    $inputs = $validation->getInputs();
   
    //パスワードのハッシュ化
    $inputs[Users::PASSWORD] = password_hash($inputs[Users::PASSWORD], PASSWORD_DEFAULT);
    
    //データベースに登録
    $db = new Users();

    $db->insertUser($inputs);

    //データベースからidを取得してニックネームと一緒にセッションに保存
    $_SESSION[Session::USER] = $db->getIdByName($inputs[Users::NAME]) + [Users::NICKNAME => $inputs[Users::NICKNAME]];

    //トップページにリダイレクト
    Common::topRedirect();
} catch (Exception $e) {
    Common::errorRedirect();
}
