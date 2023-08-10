<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//ワンタイムトークン照合
Session::verifyToken();

//セッションから推薦文id取得、取得できなければリダイレクト
$id = Session::getUnconfirmedData(Common::URL_RECOMMENDATION_DELETE, Input::RECOMMENDATION_ID);
Session::deleteFormData();

if($id == '') {
    Common::redirect(Common::URL_RECOMMENDATION);
}

//データベースから削除
try {
    $db = new Recommendations();

    $db->deleteRecommendation($id);

    //推薦文一覧にリダイレクト
    Common::redirect(Common::URL_RECOMMENDATION);
} catch (Exception $e) {
    Common::errorRedirect();
}