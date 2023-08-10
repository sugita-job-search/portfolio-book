<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//ワンタイムトークン照合
Session::verifyToken();

//登録するデータを取得、なければリダイレクト
$data = Session::getUnconfirmedDataArray(Common::URL_RECOMMENDATION_REGISTRATION);

if($data == []) {
    Common::redirect(Common::URL_RECOMMENDATION_SEARCH);
}

//ユーザーidを配列に追加
$data[Recommendations::USER_ID] = $_SESSION[Session::USER][Users::ID];

try {
    //データベースに登録
    $db = new Recommendations();

    $db->insertRecommendation($data);

    //完了画面にリダイレクト
    Common::redirect('./complete.php');
} catch (Exception $e) {
    Common::errorRedirect();
}