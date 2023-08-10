<?php
/**want_to_read_booksのクラス */
class WantToReadBooks extends Base {

    //カラム名
    const ID = 'id';
    const USER_ID = 'user_id';
    const BOOK_ID = 'book_id';

    /**
     * 指定idの本をログイン中のユーザーが読みたい本に登録しているか調べる
     * 
     * @param int $book_id
     * @return bool 登録しているときtrue
     */
    public function isWantToReadBook($book_id)
    {
        $sql = 'SELECT id FROM want_to_read_books
                WHERE user_id = :user_id AND book_id = :book_id';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION[Session::USER][Users::ID], PDO::PARAM_INT);
        $stmt->bindValue(':book_id', $book_id, PDO::PARAM_INT);

        $stmt->execute();

        $id = $stmt->fetchColumn();

        if(!$id) {
            return false;
        }

        return true;
    }

    /**
     * 指定idの本をログイン中のユーザーの読みたい本に登録
     * 
     * @param int $book_id 
     */
    public function insertWantToReadBook($book_id)
    {
        $sql = 'INSERT INTO want_to_read_books
                (user_id, book_id)
                VALUES (:user_id, :book_id)';

        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION[Session::USER][Users::ID], PDO::PARAM_INT);
        $stmt->bindValue(':book_id', $book_id, PDO::PARAM_INT);

        $stmt->execute();
    }

    /**
     * 指定idの読みたい本を削除
     * 
     * @param int $want_id
     */
    public function deleteWantToReadBook($want_id)
    {
        $sql = 'DELETE FROM want_to_read_books
                WHERE id = :id';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $want_id, PDO::PARAM_INT);

        $stmt->execute();
    }

    /**
     * ログイン中のユーザーが読みたい本を全て取得
     * 
     * @return array
     */
    public function getWantToReadBooksByUserId()
    {
        $sql = 'SELECT
                w.id
                ,w.book_id
                ,b.title
                ,b.author
                ,b.publisher
                ,b.year
                ,b.month
                ,b.series_title
                ,b.image
                FROM want_to_read_books w
                JOIN books b
                ON w.book_id = b.id
                WHERE w.user_id = :user_id
                ORDER BY w.id DESC';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION[Session::USER][Users::ID], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 指定の読みたい本idの本の情報取得、ただしログイン中のユーザーの読みたい本でない場合は取得しない
     * 
     * @param int $want_id
     * @return array|false
     */
    public function getWantToReadBookByWantToReadBookId($want_id)
    {
        $sql = 'SELECT
                b.id
                ,b.title
                ,b.author
                ,b.publisher
                ,b.year
                ,b.month
                ,b.series_title
                ,b.image
                FROM want_to_read_books w
                INNER JOIN books b
                ON w.book_id = b.id
                WHERE w.user_id = :user_id
                AND w.id = :want_id';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION[Session::USER][Users::ID], PDO::PARAM_INT);
        $stmt->bindValue(':want_id', $want_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}