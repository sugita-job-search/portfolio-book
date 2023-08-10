<?php
/**booksテーブルのクラス */
class Books extends Base {
    
    //カラム名
    const ID = 'id';
    const TITLE = 'title';
    const AUTHOR = 'author';
    const PUBLISHER = 'publisher';
    const YEAR = 'year';
    const MONTH = 'month';
    const SERIES_TITLE = 'series_title';
    const GENRE_ID = 'genre_id';
    const ISBN = 'isbn';
    const IMAGE = 'image';
    
    /**
     * 指定のisbnの本のid取得
     * 
     * @param string $isbn
     * @return string|false
     */
    public function getBookIdByIsbn($isbn)
    {
        $sql = 'SELECT id FROM books WHERE isbn = :isbn';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * 指定idの本の情報取得
     * 
     * @param int $id
     * @return array|false
     */
    public function getBookById($id)
    {
        $sql = 'SELECT
                id
                ,title
                ,author
                ,publisher
                ,year
                ,month
                ,series_title
                ,genre_id
                ,isbn
                ,image
                FROM books
                WHERE id = :id';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 指定idの本の書名と著者と書影の名前取得
     * 
     * @param int $id
     * @return string|false
     */
    public function getTitleAuthorImage($id)
    {
        $sql = 'SELECT title, author, image FROM books WHERE id = :id';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 指定idの本の画像の名前を取得
     * 
     * @param int $id
     * @return string|false
     */
    public function getImageByID($id)
    {
        $sql = 'SELECT image FROM books WHERE id = :id';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * 指定idの本の情報とジャンル名取得
     * 
     * @param int $id
     * @return array|false
     */
    public function getBookGenreByBookId($book_id)
    {
        $sql = 'SELECT
                b.id
                ,b.title
                ,b.author
                ,b.publisher
                ,b.year
                ,b.month
                ,b.series_title
                ,b.isbn
                ,b.image
                ,g.genre
                FROM books b
                JOIN genres g
                ON b.genre_id = g.id
                WHERE b.id = :book_id';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':book_id', $book_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 指定idの本とジャンルとログイン中のユーザーが読みたい本に登録しているときそのid取得
     * 
     * @param int $book_id
     * @return array|false
     */
    public function getBookGenreWantByBookId($book_id)
    {
        $sql = 'SELECT
                b.id
                ,b.title
                ,b.author
                ,b.publisher
                ,b.year
                ,b.month
                ,b.series_title
                ,b.isbn
                ,b.image
                ,g.genre
                ,w.id AS want
                FROM books b
                INNER JOIN genres g
                ON b.genre_id = g.id
                LEFT JOIN want_to_read_books w
                ON b.id = w.book_id
                AND w.user_id = :user_id
                WHERE b.id = :book_id';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':book_id', $book_id, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $_SESSION[Session::USER][Users::ID],PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 書名著者シリーズ名、isbnに一致するときはisbnから本を検索
     * 
     * @param string $str
     * @return array
     */
    public function searchBooks($str)
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
                ,w.id AS want
                FROM books b
                LEFT JOIN want_to_read_books w
                ON b.id = w.book_id
                AND w.user_id = :user_id
                WHERE CONCAT(b.title, b.author, b.series_title) LIKE :str';

        //検索する文字列がisbnの形式に合致するときはisbnも検索対象にする
        $isbn = Common::validateIsbn($str);
        if($isbn != '') {
            $sql .= ' OR b.isbn = :isbn';
        }

        //検索文字列にワイルドカード付加
        $str = "%{$str}%";
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION[Session::USER][Users::ID], PDO::PARAM_INT);
        $stmt->bindValue(':str', $str, PDO::PARAM_STR);
        
        if($isbn != '') {
            $stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 指定項目が引数と一致する本の情報と、ログイン中のユーザーが読みたい本に登録しているときはそのidを取得
     * 
     * @param string $column_name テーブルのカラム名
     * @param string $str
     * 
     * @return array
     */
    public function searchBooksByAuthorOrSeries($column_name, $str)
    {
        $sql = "SELECT
                b.id
                ,b.title
                ,b.author
                ,b.publisher
                ,b.year
                ,b.month
                ,b.series_title
                ,b.image
                ,w.id AS want
                FROM books b
                LEFT JOIN want_to_read_books w
                ON b.id = w.book_id
                AND w.user_id = :user_id
                WHERE {$column_name} LIKE :str";

        //検索文字列にワイルドカード付加
        $str = "%{$str}%";
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION[Session::USER][Users::ID], PDO::PARAM_INT);
        $stmt->bindValue(':str', $str, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 本を登録
     * 
     * @param $array $array カラム名をキーとした登録内容の配列
     */
    public function insertBook($array)
    {
        $sql = 'INSERT INTO books
                (title, author, publisher, year, month, series_title, genre_id, isbn, image)
                VALUES
                (:title, :author, :publisher, :year, :month, :series_title, :genre_id, :isbn, :image)';

        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':title', $array[Books::TITLE], PDO::PARAM_STR);
        $stmt->bindValue(':author', $array[Books::AUTHOR], PDO::PARAM_STR);
        $stmt->bindValue(':publisher', $array[Books::PUBLISHER], PDO::PARAM_STR);
        $stmt->bindValue(':year', $array[Books::YEAR], PDO::PARAM_INT);
        $stmt->bindValue(':month', $array[Books::MONTH], PDO::PARAM_INT);
        $stmt->bindValue(':series_title', $array[Books::SERIES_TITLE], PDO::PARAM_STR);
        $stmt->bindValue(':genre_id', $array[Books::GENRE_ID], PDO::PARAM_INT);
        $stmt->bindValue(':isbn', $array[Books::ISBN], PDO::PARAM_STR);
        $stmt->bindValue(':image', $array[Books::IMAGE], PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * 書誌情報上書き
     * 
     * @param $array $array カラム名をキーとした登録内容の配列
     */
    public function updateBook($array)
    {
        $sql = 'UPDATE books
                SET title = :title
                ,author = :author
                ,publisher = :publisher
                ,year = :year
                ,month = :month
                ,series_title = :series_title
                ,genre_id = :genre_id
                ,image = :image
                WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $array[Books::ID], PDO::PARAM_INT);
        $stmt->bindValue(':title', $array[Books::TITLE], PDO::PARAM_STR);
        $stmt->bindValue(':author', $array[Books::AUTHOR], PDO::PARAM_STR);
        $stmt->bindValue(':publisher', $array[Books::PUBLISHER], PDO::PARAM_STR);
        $stmt->bindValue(':year', $array[Books::YEAR], PDO::PARAM_INT);
        $stmt->bindValue(':month', $array[Books::MONTH], PDO::PARAM_INT);
        $stmt->bindValue(':series_title', $array[Books::SERIES_TITLE], PDO::PARAM_STR);
        $stmt->bindValue(':genre_id', $array[Books::GENRE_ID], PDO::PARAM_INT);
        $stmt->bindValue(':image', $array[Books::IMAGE], PDO::PARAM_STR);

        $stmt->execute();
    }
}