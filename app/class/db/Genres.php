<?php
/**genresテーブルのクラス */
class Genres extends Base {

    //カラム名
    const ID = 'id';
    const GENRE = 'genre';

    /**
     * 全ジャンルを取得
     * 
     * @return array
     */
    public function getGenres()
    {
        $sql = 'SELECT * FROM genres';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $genres = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return $genres;
    }

    /**
     * 指定idのジャンル名取得
     * 
     * @param int $genre_id
     * @return string
     */
    public function getGenre($genre_id)
    {
        $sql = 'SELECT genre FROM genres WHERE id = :genre_id';

        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':genre_id', $genre_id, PDO::PARAM_INT);
        
        $stmt->execute();

        $genre = $stmt->fetchColumn();

        if($genre == false) {
            return '';
        }

        return $genre;
    }

    /**
     * 複数idのジャンル名取得
     * 
     * @param array $ids ジャンルidの配列
     * @param int $count 配列の要素の数
     * @return array
     */
    public function getGenresByGenreId($ids, $count)
    {
        //in句作成
        $in = substr(str_repeat(',?', $count), 1);

        //sql文作成
        $sql = "SELECT genre FROM genres WHERE id IN ($in)";

        $stmt = $this->dbh->prepare($sql);

        $n = 0;
        foreach($ids as $id) {
            $stmt->bindValue(++ $n, $id, PDO::PARAM_INT);
        }
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}