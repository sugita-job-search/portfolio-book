<?php

/**セッションを操作するクラス */
class Session
{
    /**ログイン時のユーザー情報を保存するセッション変数名 */
    const USER = 'user';

    /**ワンタイムトークンを保存するセッション変数名 */
    const TOKEN = 'token';

    /**エラーメッセージを保存するセッション変数名 */
    const MESSAGE = 'message';

    /**入力内容を保存するセッション変数名 */
    const HISTORY = 'history';

    /**複数の画面をまたいでデータベースに登録する内容を保存するセッション変数名 */
    const UNCONFIRMED = 'unconfirmed';

    /**ファイルそのものをセッションに保存するときつけるキー */
    const FILE = 'file';

    /**ファイルのmimeタイプをセッションに保存するときつけるキー */
    const FILE_TYPE = 'file_type';


    /**
     * ログイン状態にないときログイン画面にリダイレクト
     */
    public static function confirmLogin()
    {
        if (!isset($_SESSION[self::USER])) {
            Common::redirect(Common::URL_LOGIN);
        }
    }

    /**
     * ワンタイムトークンをセッションに保存した上で返却
     * 
     * @return string
     */
    public static function generateToken()
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::TOKEN] = $token;

        return $token;
    }

    /**
     * ワンタイムトークンを照合して失敗したらトップページにリダイレクト
     */
    public static function verifyToken()
    {
        if (!isset($_SESSION[self::TOKEN]) || !isset($_POST[Input::TOKEN]) || $_SESSION[self::TOKEN] != $_POST[Input::TOKEN]) {
            Common::topRedirect();
        }
    }

    /**
     * セッションにエラーメッセージを保存
     * 
     * @param string $url メッセージを表示するURL
     * @param string $item_name 項目名
     * @param string $message エラーメッセージ
     */
    public static function saveMessage($url, $item_name, $message)
    {
        $_SESSION[self::MESSAGE][$url][$item_name] = $message;
    }

    /**
     * セッションにエラーメッセージが保存されているときエラーメッセージ部分のHTMLを返却
     * 
     * @param string $url メッセージを表示するURL
     * @param string $item_name 項目名
     * @return string
     */
    public static function getMessage($url, $item_name)
    {
        if (isset($_SESSION[self::MESSAGE][$url][$item_name])) {
            $message = '<div class="error-message">';
            $message .= $_SESSION[self::MESSAGE][$url][$item_name];
            $message .= '</div>';

            return $message;
        }

        return '';
    }

    /**
     * セッションunconfirmedにデータを保存
     * 
     * @param string $url 入力フォームのurl
     * @param string $column_name データベースに登録するときのカラム名
     * @param string|int $data
     */
    public static function saveUnconfirmedData($url, $column_name, $data)
    {
        $_SESSION[self::UNCONFIRMED][$url][$column_name] = $data;
    }

    /**
     * unconfirmedに保存されているデータを返却
     * 
     * @param string $url 入力フォームのurl
     * @param string $column_name データベースに登録するときのカラム名
     * @return string|int
     */
    public static function getUnconfirmedData($url, $column_name)
    {
        if (isset($_SESSION[self::UNCONFIRMED][$url][$column_name])) {
            return $_SESSION[self::UNCONFIRMED][$url][$column_name];
        }

        return '';
    }

    /**
     * unconfirmedに保存されているデータを配列ごと返却
     * 
     * @param string $url 入力フォームのurl
     * @return array
     */
    public static function getUnconfirmedDataArray($url)
    {
        if (isset($_SESSION[self::UNCONFIRMED][$url])) {
            return $_SESSION[self::UNCONFIRMED][$url];
        }

        return [];
    }


    /**セッションに保存されているエラーメッセージと入力内容を削除 */
    public static function deleteMessageAndHistory()
    {
        unset($_SESSION[self::MESSAGE]);
        unset($_SESSION[self::HISTORY]);
    }

    /**セッションに保存されているフォーム関係のデータを削除 */
    public static function deleteFormData()
    {
        unset($_SESSION[self::MESSAGE]);
        unset($_SESSION[self::HISTORY]);
        unset($_SESSION[self::UNCONFIRMED]);
    }
}
