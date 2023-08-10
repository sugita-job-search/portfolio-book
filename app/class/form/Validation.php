<?php
/**バリデーションのクラス */
class Validation extends Input {

    /**不正な値が送信されたときのエラーメッセージ */
    const INVALID_VALUE_MESSAGE ='不正な値が送信されました';

    /**登録しようとしたアカウント名がすでに登録されているときのエラーメッセージ */
    const NAME_DUPLICATION_MESSAGE = 'そのアカウント名はすでに使用されています';

    /**パスワードの形式が違った場合のエラーメッセージ */
    const INVALID_PASSWORD_MESSAGE = 'パスワードは8~16文字の半角英数字記号で入力してください';

    /**ログインに失敗したときのエラーメッセージ */
    const FAILURE_LOGIN_MESSAGE = 'アカウント名またはパスワードが違います';

    /**ISBNの形式が違った場合のエラーメッセージ */
    const INVALID_ISBN_MESSAGE = 'ISBNを正しく入力してください';

    /**著者名の文字数が多かった場合のエラーメッセージ */
    const LONG_AUTHOR_MESSAGE = '文字数が多すぎます。多数の著者がいる場合は主要な著者のみを入力してください。';

    /**出版年月が正しくないときのエラーメッセージ */
    const INVALID_YEAR_AND_MONTH_MESSAGE = '出版年月を正しく入力してください';

    /**ジャンルが選択されていないときのエラーメッセージ */
    const EMPTY_GENRE_MESSAGE = 'ジャンルを選択してください';

    /**ファイルアップロードに失敗したときのエラーメッセージ */
    const FAILURE_UPLOAD_MESSAGE = 'ファイルのアップロードに失敗しました';

    /**非対応の形式のファイルがアップロードされたときのエラーメッセージ */
    const INVALID_FILE = 'その形式のファイルはアップロードできません';


    /**
     * 入力がないときのエラーメッセージを返す
     * 
     * @param string $item_name
     * @return string
     */
    public function getMessageForEmptyInput($item_name)
    {
        return $item_name. 'を入力してください';
    }

    /**
     * 文字数上限を超えたときのエラーメッセージを返す
     * 
     * @param string $item_name 項目名
     * @return string
     */
    public function getMessageForLongInput($item_name)
    {
        return $item_name. 'は'. parent::MAX_LENGTH_ARRAY[$item_name]. '文字以内で入力してください';
    }


    /**
     * フォームが未入力のときエラーメッセージをセッションに保存
     * 
     * @param string $item_name 項目名
     * @return bool エラーメッセージが保存されたときtrue、されなかったときfalse
     */
    public function saveMessageForEmptyInput($item_name)
    {
        if ($this->inputs[self::COLUMN_NAMES[$item_name]] === '') {
            Session::saveMessage($this->url, $item_name, $this->getMessageForEmptyInput($item_name));
            return true;
        }

        return false;
    }

    /**
     * 文字数が超過している場合に対応するエラーメッセージをセッションに保存
     * 
     * @param string $item_name 項目名
     * @return bool エラーメッセージが保存されたときtrue、されなかったときfalse
     */
    public function saveMessageForLongInput($item_name)
    {
        if (mb_strlen($this->inputs[parent::COLUMN_NAMES[$item_name]]) > parent::MAX_LENGTH_ARRAY[$item_name]) {
            Session::saveMessage($this->url, $item_name, $this->getMessageForLongInput($item_name));
            return true;   
        }

        return false;
    }

    /**
     * フォームに未入力のときと文字数が超過している場合に対応するエラーメッセージをセッションに保存
     * 
     * @param string $item_name 項目名
     * @return bool エラーメッセージが保存されたときtrue、されなかったときfalse
     */
    public function saveMessageForEmptyOrLongInput(string $item_name)
    {
        if ($this->inputs[self::COLUMN_NAMES[$item_name]] === '') {
            Session::saveMessage($this->url, $item_name, $this->getMessageForEmptyInput($item_name));
            return true;
        }
        if (mb_strlen($this->inputs[self::COLUMN_NAMES[$item_name]]) > parent::MAX_LENGTH_ARRAY[$item_name]) {
            Session::saveMessage($this->url, $item_name, $this->getMessageForLongInput($item_name));
            return true;
        }

        return false;
    }

    /**
     * 登録済みのアカウント名を登録しようとしたときエラーメッセージをセッションに保存
     * 
     * @return bool エラーメッセージが保存されたときtrue、されなかったときfalse
     */
    public function saveMessageForDuplicateName()
    {
        $db = new Users();

        if($db->existsUserName($this->inputs[Users::NAME])) {
            Session::saveMessage($this->url, parent::NAME, self::NAME_DUPLICATION_MESSAGE);
            return true;
        }
        
        return false;
    }

    /**
     * パスワードの形式が正しくないときエラーメッセージをセッションに保存 
     * 
     * @return bool エラーメッセージが保存されたときtrue、されなかったときfalse
    */
    public function saveMessageForInvalidPassword()
    {
        if(preg_match('/\A[\x20-~]{8,16}\Z/', $this->inputs[Users::PASSWORD]) != 1) {
            Session::saveMessage($this->url, parent::PASSWORD, self::INVALID_PASSWORD_MESSAGE);
            return true;
        }

        return false;
    }

    /**
     * ジャンルidが正しくないときエラーメッセージをセッションに保存
     * 
     * @param string $item_name 項目名
     * @return bool エラーメッセージが保存されたときtrue、されなかったときfalse
     */
    public function saveMessageForInvalidGenreId($item_name)
    {
        if($this->inputs[self::COLUMN_NAMES[$item_name]] === '') {
            Session::saveMessage($this->url, $item_name, self::INVALID_VALUE_MESSAGE);
            return true;
        }
        
        if($this->inputs[self::COLUMN_NAMES[$item_name]] === 0) {
            return false;
        }
        
        $db = new Genres();

        if($db->getGenre($this->inputs[self::COLUMN_NAMES[$item_name]]) == '') {
            Session::saveMessage($this->url, $item_name, self::INVALID_VALUE_MESSAGE);
            return true;
        }
        
        return false;
    }

    /**
     * ジャンルidが正しくないときとジャンルが選択されていないときエラーメッセージをセッションに保存
     * 
     * @param string $item_name 項目名 初期値定数GENRE
     * @return bool エラーメッセージが保存されたときtrue
     */
    public function saveMessageForInvalidAndEmptyGenre($item_name = parent::GENRE)
    {
        if($this->inputs[self::COLUMN_NAMES[$item_name]] ===''){
            Session::saveMessage($this->url, $item_name, self::INVALID_VALUE_MESSAGE);
            return true;
        }

        if($this->inputs[self::COLUMN_NAMES[$item_name]] === 0) {
            Session::saveMessage($this->url, $item_name, self::EMPTY_GENRE_MESSAGE);
            return true;
        }

        $db = new Genres();

        $genre = $db->getGenre($this->inputs[self::COLUMN_NAMES[$item_name]]);

        if($genre == '') {
            Session::saveMessage($this->url, $item_name, self::INVALID_VALUE_MESSAGE);
            return true;
        }
        
        return false;
    }

    /**
     * 改行コードを\nに統一して字数が既定値より多いときエラーメッセージをセッションに保存
     * 
     * @param string $item_name 項目名 初期値定数AUTHOR
     * @return bool エラーメッセージが保存されたときtrue、されなかったときfalse
     */
    public function saveMessageForLongAuthor($item_name = parent::AUTHOR)
    {
        $this->inputs[self::COLUMN_NAMES[$item_name]] = str_replace(["\r\n", "\r"], "\n", $this->inputs[self::COLUMN_NAMES[$item_name]]);
        
        if(mb_strlen($this->inputs[self::COLUMN_NAMES[$item_name]]) > parent::MAX_LENGTH_ARRAY[$item_name]) {
            Session::saveMessage($this->url, $item_name, self::LONG_AUTHOR_MESSAGE);
            return true;
        }

        return false;
    }

    /**
     * 出版年月が正しくないときエラーメッセージをセッションに保存、出版年が全角のときは半角に変換
     * 
     * @return bool エラーメッセージが保存されたときtrue、されなかったときfalse
     */
    public function saveMessageForInvalidYearAndMonth()
    {
        //月が不正な値
        if($this->inputs[Books::MONTH] == '') {
            Session::saveMessage($this->url, parent::YEAR_AND_MONTH, self::INVALID_YEAR_AND_MONTH_MESSAGE);
            return true;
        }
        
        //年未入力
        if($this->inputs[Books::YEAR] == '') {
            Session::saveMessage($this->url, parent::YEAR_AND_MONTH, $this->getMessageForEmptyInput(parent::YEAR_AND_MONTH));
            return true;
        }

        //年を半角数字に変換
        $this->inputs[Books::YEAR] = mb_convert_kana($this->inputs[Books::YEAR], 'n');

        //現在の年度を取得
        $date = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
        $year = $date->format('Y');

        //年が1950以上現在の年度+2の範囲の整数でないときエラーメッセージをセッションに保存
        $options = ['options'=>
                        ['min_range'=>1950,
                        'max_range'=> $year + 2]
                        ];
        if(!filter_var($this->inputs[Books::YEAR], FILTER_VALIDATE_INT, $options)) {
            Session::saveMessage($this->url, parent::YEAR_AND_MONTH, self::INVALID_YEAR_AND_MONTH_MESSAGE);
            return true;
        }

        return false;
    }
    
}