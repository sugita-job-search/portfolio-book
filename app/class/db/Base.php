<?php
/**データベース操作のクラス */
class Base {

    /**データベース名 */
    const DB_NAME = 'book';

    /**ホスト名 */
    const DB_HOST = 'localhost';

    /**ユーザー名 */
    const DB_USER = 'root';

    /**パスワード */
    const DB_PASS = '';
    
    /**pdoインスタンスを代入する静的プロパティ */
    private static $pdo;

    /**子クラスがpdoインスタンスを利用するためのプロパティ */
    protected $dbh;
    
    /**
     * コンストラクタ
     * データベースに接続してエラーモード設定
     */
    public function __construct()
    {
        if (!isset(self::$pdo)) {
            $dsn = 'mysql:dbname='. self::DB_NAME. ';host='. self::DB_HOST. ';charset=utf8';
            self::$pdo = new PDO($dsn, self::DB_USER, self::DB_PASS);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        $this->dbh = self::$pdo;
    }

}