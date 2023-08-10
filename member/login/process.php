<?php
require_once('../../app/config.php');

Session::deleteFormData();

//ワンタイムトークン照合
Session::verifyToken();

$input = new Input(Common::URL_LOGIN);

//アカウント名サニタイズ
$name = $input->sanitizePost(Users::NAME);
$pass = $input->filterPost(Users::PASSWORD);

//アカウント名かパスワード未入力のときエラーメッセージと入力内容をセッションに保存してリダイレクト
if($name === '' || $pass === '') {
    Session::saveMessage(Common::URL_LOGIN, 'login', Validation::FAILURE_LOGIN_MESSAGE);
    $_SESSION[Session::HISTORY][Common::URL_LOGIN][Users::NAME] = $name;
    Common::redirect(Common::URL_LOGIN);
}

//データベースからユーザー情報取得
try {
    $db = new Users();

    $user = $db->getUserByName($name);

    //データが取得できなければエラーメッセージと入力内容をセッションに保存してリダイレクト
    if(!$user) {
        Session::saveMessage(Common::URL_LOGIN, 'login', Validation::FAILURE_LOGIN_MESSAGE);
        $_SESSION[Session::HISTORY][Common::URL_LOGIN][Users::NAME] = $name;
        Common::redirect(Common::URL_LOGIN);
    }

    //パスワード照合に失敗したらエラーメッセージと入力内容をセッションに保存してリダイレクト
    if(!password_verify($pass, $user[Users::PASSWORD])) {
        Session::saveMessage(Common::URL_LOGIN, 'login', Validation::FAILURE_LOGIN_MESSAGE);
        $_SESSION[Session::HISTORY][Common::URL_LOGIN][Users::NAME] = $name;
        Common::redirect(Common::URL_LOGIN);
    }

    //照合に成功したらセッションにidとニックネーム保存
    unset($user[Users::PASSWORD]);
    $_SESSION[Session::USER] = $user;

    //トップページにリダイレクト
    Common::topRedirect();
} catch (Exception $e) {
    Common::errorRedirect();
}