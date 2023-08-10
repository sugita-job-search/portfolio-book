<?php
/**Usersテーブルのクラス */
class Users extends Base {

    //カラム名
    const ID = 'id';

    const NAME = 'name';

    const NICKNAME = 'nickname';

    const PASSWORD = 'password';

    const GENRE_ID = 'genre_id';

    /**
     * アカウント名が登録されているか調べる
     * 
     * @param string $name 調べるアカウント名
     * @return bool
     */
    public function existsUserName($name)
    {
        $sql = 'SELECT count(id) FROM users WHERE name = :name';

        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':name', $name, PDO::PARAM_STR);

        $stmt->execute();

        $count = $stmt->fetchColumn();

        if ($count === '0') {
            return false;
        }
            
        return true;
    }

    /**
     * 新しいレコード登録
     * 
     * @param $array $array カラム名をキーとした登録内容の配列
     */
    public function insertUser($array)
    {
        $sql = 'INSERT INTO users (name, nickname, password, genre_id) VALUES (:name, :nickname, :password, :genre_id)';
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':name', $array[self::NAME], PDO::PARAM_STR);
        $stmt->bindValue(':nickname', $array[self::NICKNAME], PDO::PARAM_STR);
        $stmt->bindValue(':password', $array[self::PASSWORD], PDO::PARAM_STR);
        $stmt->bindValue(':genre_id', $array[self::GENRE_ID], PDO::PARAM_INT);

        $stmt->execute();
    }

    /**
     * 指定idのレコード更新
     * 
     * @param $array $array カラム名をキーとした登録内容の配列
     */
    public function updateUser($array)
    {
        $sql = 'UPDATE users
                SET name = :name
                ,nickname = :nickname
                ,password = :password
                ,genre_id = :genre_id
                WHERE id = :id';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $array[self::ID], PDO::PARAM_INT);
        $stmt->bindValue(':name', $array[self::NAME], PDO::PARAM_STR);
        $stmt->bindValue(':nickname', $array[self::NICKNAME], PDO::PARAM_STR);
        $stmt->bindValue(':password', $array[self::PASSWORD], PDO::PARAM_STR);
        $stmt->bindValue(':genre_id', $array[self::GENRE_ID], PDO::PARAM_INT);

        $stmt->execute();
    }

    /**
     * 指定のアカウント名のidとニックネームとパスワードを取得
     * 
     * @param string $name
     * @return array|false
     */
    public function getUserByName($name)
    {
        $sql = 'SELECT id, nickname, password
                FROM users
                WHERE name = :name';

        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':name', $name, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 指定のアカウント名のidを取得
     * 
     * @param string $name
     * @return array|false
     */
    public function getIdByName($name)
    {
        $sql = 'SELECT id
                FROM users
                WHERE name = :name';

        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':name', $name, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 指定のidのアカウント名取得
     * 
     * @param int $id
     * @return string|false
     */
    public function getNameById($id)
    {
        $sql = 'SELECT name
                FROM users
                WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * 指定idユーザーの好きなジャンルとジャンル名取得
     * 
     * @param int $user_id
     * @return array
     */
    public function getGenreByUserId($user_id)
    {
        $sql = 'SELECT genre_id
                ,g.genre
                FROM users u
                LEFT OUTER JOIN genres g
                ON u.genre_id = g.id
                WHERE u.id = :user_id';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * 指定のidのアカウント名とニックネームと好きなジャンル取得
     * 
     * @param int $id
     * @return array|false
     */
    public function getUserById($id)
    {
        $sql = 'SELECT name, nickname, genre_id
                FROM users
                WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * 指定idのユーザー情報と好きなジャンルのジャンル名取得
     * 
     * @param int $user_id
     * @return array|false
     */
    public function getUserGenreByUserId($user_id)
    {
        $sql = 'SELECT u.name
                ,u.nickname
                ,g.genre
                FROM users u
                LEFT JOIN genres g
                ON u.genre_id = g.id
                WHERE u.id = :id';
        
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}