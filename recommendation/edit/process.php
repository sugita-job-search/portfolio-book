<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//セッションに保存されている推薦文のid取得、なければリダイレクト
$id_array = Session::getUnconfirmedDataArray(Common::URL_RECOMMENDATION_EDIT);

if($id_array == []) {{
    Common::redirect(Common::URL_RECOMMENDATION);
}}

//セッションから不要なデータ削除
Session::deleteFormData();

//ワンタイムトークン照合
Session::verifyToken();

$validation = new Validation(Common::URL_RECOMMENDATION_EDIT);

//サニタイズ
$validation->sanitizePost(Recommendations::RECOMMENDATION);

//未入力または字数オーバーのときエラーメッセージと入力履歴をセッションに保存してリダイレクト
if($validation->saveMessageForEmptyOrLongInput(Input::RECOMMENDATION)) {
    $validation->saveHistories();
    Common::redirect(Common::URL_RECOMMENDATION_EDIT. '?'. Input::RECOMMENDATION_ID. '='. $id_array[Recommendations::ID]. '#form');
}

//データベースに登録するデータの配列作成
$data = $id_array + $validation->getInputs();

//データベースを更新
try {
    $db = new Recommendations();

    $db->updateRecommendation($data);

    Common::redirect(Common::URL_RECOMMENDATION);
} catch (Exception $e) {
    Common::errorRedirect();
}