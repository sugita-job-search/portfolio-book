<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//セッションから本のidを取得、なければリダイレクト
$id_array = Session::getUnconfirmedDataArray(Common::URL_BOOK_EDIT);
if($id_array == []) {
    Common::topRedirect();
}

Session::deleteFormData();

//ワンタイムトークン照合
Session::verifyToken();

$validation = new Validation(Common::URL_BOOK_EDIT);

try {
    //idの本の画像の名前を取得
    $db = new Books();
    $image = $db->getImageByID($id_array[Books::ID]);

    //画像の名前が取得できなかったらリダイレクト
    if($image === false) {
        Common::topRedirect();
    }

    //入力内容をフィルタリングして$inputsに追加
    $validation->sanitizePost(Books::TITLE);
    $validation->sanitizePost(Books::AUTHOR);
    $validation->sanitizePost(Books::PUBLISHER);
    $validation->sanitizePost(Books::YEAR);
    $validation->filterMonth(Books::MONTH);
    $validation->sanitizePost(Books::SERIES_TITLE);
    $validation->filterNaturalNumberPost(Books::GENRE_ID);

    //バリデーションエラーが起こるとエラーメッセージをセッションに保存
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

    //許容する形式のファイルがアップロードされていたとき拡張子を代入する変数
    $ex = '';
    
    //ファイルアップロードに失敗したときエラーメッセージ保存
    if(isset($_FILES['image']['error'])) {
        if($_FILES['image']['error'] != 0 && $_FILES['image']['error'] != 4) {
        Session::saveMessage(Common::URL_BOOK_EDIT, Input::IMAGE, Validation::FAILURE_UPLOAD_MESSAGE);
        //ファイルがアップロードされたときファイルの形式がpngかjpegでなかったときエラーメッセージ保存
        } elseif($_FILES['image']['error'] == 0) {
            if($_FILES['image']['type'] == 'image/png') {
                $ex = '.png';
            } elseif($_FILES['image']['type'] == 'image/jpeg') {
                $ex = '.jpg';
            } else {
                Session::saveMessage(Common::URL_BOOK_EDIT, Input::IMAGE, Validation::INVALID_FILE);
            }
        }
    }

    //エラーメッセージが保存されたとき入力履歴をセッションに保存してリダイレクト
    if(isset($_SESSION[Session::MESSAGE])) {
        $validation->saveHistories();
        Common::redirect(Common::URL_BOOK_EDIT.'?'.Input::BOOK_ID.'='.$id_array[Books::ID]);
    }

    //ファイルがアップロードされたときは名前をつけて保存、失敗したときエラーメッセージをセッションに保存してリダイレクト
    if($ex != '') {
        $date = new DateTime();
        $file_name = md5($date->format('c')). $ex;
        if(!move_uploaded_file($_FILES['image']['tmp_name'], '../../img/'. $file_name)) {
            Session::saveMessage(Common::URL_BOOK_EDIT, Input::IMAGE, Validation::FAILURE_UPLOAD_MESSAGE);
            $validation->saveHistories();
            Common::redirect(Common::URL_BOOK_EDIT.'?'.Input::BOOK_ID.'='.$id_array[Books::ID]);
        }
        
        //前のファイルが存在するとき削除
        if($image != '') {
            unlink('../../img/'. $image);
        }

        //データベースに新しい画像の名前を保存
        $image = $file_name;
    }

    //バリデーション後の入力内容取得、idの配列と結合
    $data = $id_array + $validation->getInputs();

    //$dataに画像の名前追加
    $data[Books::IMAGE] = $image;

    //データベースに保存
    $db->updateBook($data);

    //本のページにリダイレクト
    Common::redirect(Common::URL_BOOK.'?'.Input::BOOK_ID.'='.$id_array[Books::ID]);

} catch (Exception $e) {
    Common::errorRedirect();
}