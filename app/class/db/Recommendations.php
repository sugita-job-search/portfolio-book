<?php
/**recommendationsテーブルのクラス */
class Recommendations extends Base {
    //カラム名
    const ID = 'id';

    const RECOMMENDATION = 'recommendation';

    const USER_ID = 'user_id';

    const BOOK_ID = 'book_id';

    /**
     * 推薦文登録
     * 
     * @param array $array カラム名をキーとした登録内容の配列
     */
    public function insertRecommendation($array)
    {
        $sql = 'INSERT INTO recommendations
                (recommendation, user_id, book_id)
                VALUES (:recommendation, :user_id, :book_id)';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':recommendation', $array[self::RECOMMENDATION], PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $array[self::USER_ID], PDO::PARAM_INT);
        $stmt->bindValue(':book_id', $array[self::BOOK_ID], PDO::PARAM_INT);

        $stmt->execute();
    }
    
    /**
     * 推薦文更新
     * 
     * @param array $array カラム名をキーとした登録内容の配列
     */
    public function updateRecommendation($array)
    {
        $sql = 'UPDATE recommendations
                SET recommendation = :recommendation
                WHERE id = :id';
        
        $stmt = $this->dbh->prepare($sql);
        
        $stmt->bindValue(':recommendation', $array[self::RECOMMENDATION], PDO::PARAM_STR);
        $stmt->bindValue(':id', $array[self::ID], PDO::PARAM_INT);

        $stmt->execute();
    }

    /**
     * 推薦文削除
     * 
     * @param int $id
     */
    public function deleteRecommendation($id)
    {
        $sql = 'DELETE FROM recommendations WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
    }

    /**
     * 推薦文と投稿者のニックネームと推薦文が書かれた本の書名著者書影とログイン中のユーザーが欲しい本に登録している場合はそのidを全件取得
     * 
     * @return array
     */
    public function getAllRecommendations()
    {
        $sql = 'SELECT
                r.id
                ,r.recommendation
                ,r.book_id
                ,u.nickname
                ,b.title
                ,b.author
                ,b.image
                ,w.id AS want
                FROM 
                recommendations r
                INNER JOIN users u
                ON r.user_id = u.id
                INNER JOIN books b
                ON r.book_id = b.id
                LEFT OUTER JOIN want_to_read_books AS w
                ON b.id = w.book_id
                AND w.user_id = :user_id
                ORDER BY r.id DESC';

        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION[Session::USER][Users::ID], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 指定ジャンルidの推薦文と投稿者のニックネームと推薦文が書かれた本の書名著者書影とログイン中のユーザーが欲しい本に登録している場合はそのidを取得
     * 
     * @param int $genre_id ジャンルid
     * @return array
     */
    public function getRecommendationsByGenre($genre_id)
    {
        $sql = "SELECT
                r.id
                ,r.recommendation
                ,r.book_id
                ,u.nickname
                ,b.title
                ,b.author
                ,b.image
                ,g.genre
                ,w.id AS want
                FROM 
                recommendations r
                INNER JOIN users u
                ON r.user_id = u.id
                INNER JOIN books b
                ON r.book_id = b.id
                INNER JOIN genres g
                ON b.genre_id = g.id
                LEFT OUTER JOIN want_to_read_books AS w
                ON b.id = w.book_id
                AND w.user_id = :user_id
                WHERE g.id = :genre_id
                ORDER BY r.id DESC";

        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION[Session::USER][Users::ID], PDO::PARAM_INT);
        $stmt->bindValue(':genre_id', $genre_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 指定ジャンルidの推薦文と投稿者のニックネームと推薦文が書かれた本の書名著者書影とログイン中のユーザーが欲しい本に登録している場合はそのidを取得
     * 
     * @param array $ids ジャンルidの配列
     * @param int $count 配列の要素数
     * @return array
     */
    public function getRecommendationsByGenres($ids, $count)
    {
        //in句を作成
        $in = substr(str_repeat(',?', $count), 1);

        //sql文作成
        $sql = "SELECT
                r.id
                ,r.recommendation
                ,r.book_id
                ,u.nickname
                ,b.title
                ,b.author
                ,b.image
                ,g.genre
                ,w.id AS want
                FROM 
                recommendations r
                INNER JOIN users u
                ON r.user_id = u.id
                INNER JOIN books b
                ON r.book_id = b.id
                INNER JOIN genres g
                ON b.genre_id = g.id
                LEFT OUTER JOIN want_to_read_books AS w
                ON b.id = w.book_id
                AND w.user_id = ?
                WHERE g.id
                IN ({$in})
                ORDER BY r.id DESC";

        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(1, $_SESSION[Session::USER][Users::ID], PDO::PARAM_INT);

        $n = 1;
        foreach($ids as $i) {
            $stmt->bindValue(++ $n, $i, PDO::PARAM_INT);
        } 

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 指定idの本の推薦文と投稿者のニックネーム取得
     * 
     * @param int $id
     * @return array
     */
    public function getRecommendationsByBookId($id)
    {
        $sql = 'SELECT r.recommendation, u.nickname
                FROM recommendations r
                JOIN users u
                ON r.user_id = u.id
                WHERE r.book_id = :id
                ORDER BY r.id DESC';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ログイン中のユーザーの推薦文と書名著者書影を推薦文id降順取得
     * 
     * @return array
     */
    public function getRecommendationsByUser()
    {
        $sql = 'SELECT
                r.id
                ,r.recommendation
                ,r.book_id
                ,b.title
                ,b.author
                ,b.image
                FROM recommendations r
                JOIN books b
                ON r.book_id = b.id
                WHERE r.user_id = :id
                ORDER BY r.id DESC';

        $stmt = $this->dbh->prepare($sql);

        $stmt ->bindValue(':id', $_SESSION[Session::USER][Users::ID], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ログイン中のユーザーの指定idの推薦文とそれが書かれた本の情報取得
     * 
     * @param int $recommendation_id
     * @return array|false
     */
    public function getRecommendationBookByRecommendationId($recommendation_id)
    {
        $sql = 'SELECT
                r.recommendation
                ,b.title
                ,b.author
                ,b.publisher
                ,b.year
                ,b.month
                ,b.series_title
                ,b.isbn
                ,b.image
                ,g.genre
                FROM recommendations r
                JOIN books b
                ON r.book_id = b.id
                JOIN genres g
                ON b.genre_id = g.id
                WHERE r.id = :id
                AND r.user_id = :user_id';

        $stmt = $this->dbh->prepare($sql);

        $stmt ->bindValue(':id', $recommendation_id, PDO::PARAM_INT);
        $stmt ->bindValue(':user_id', $_SESSION[Session::USER][Users::ID], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}