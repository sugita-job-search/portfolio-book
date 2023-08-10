<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

Session::deleteFormData();

$validation = new Validation(Common::URL_MEMBER_EDIT);

//入力内容をフィルタリングして$inputsに追加
$name = $validation->sanitizePost(Users::NAME);
$validation->sanitizePost(Users::NICKNAME);
$validation->filterPost(Users::PASSWORD);
$validation->filterNaturalNumberPost(Users::GENRE_ID);

try {
    //バリデーションエラーが起こるとエラーメッセージをセッションに保存
    //アカウント名
    //未入力または字数オーバー
    if (!$validation->saveMessageForEmptyOrLongInput(Input::NAME)) {
        //登録済みのアカウント名
        $db = new Users();

        $old_name = $db->getNameById($_SESSION[Session::USER][Users::ID]);
        if($name != $old_name) {
            $validation->saveMessageForDuplicateName();
        }
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
        unset($_SESSION[Session::HISTORY][Common::URL_MEMBER_EDIT][Users::PASSWORD]);
        Common::redirect(Common::URL_MEMBER_EDIT);
    }
    
    $inputs = $validation->getInputs();
    
    //パスワードのハッシュ化
    $inputs[Users::PASSWORD] = password_hash($inputs[Users::PASSWORD], PASSWORD_DEFAULT);

    //idを$inputsに追加
    $inputs[Users::ID] = $_SESSION[Session::USER][Users::ID];
    
    //データベース更新
    $db->updateUser($inputs);

    //新しいニックネームをセッションに保存
    $_SESSION[Session::USER][Users::NICKNAME] = $inputs[Users::NICKNAME];

    //会員情報確認ページにリダイレクト
    Common::redirect(Common::URL_MEMBER);
} catch (Exception $e) {
    Common::errorRedirect();
}