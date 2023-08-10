<?php
/**入力内容を操作するクラス */
class Input {
    
    /**入力項目名 */
    const NAME = 'アカウント名';
    
    const NICKNAME = 'ニックネーム';

    const PASSWORD = 'パスワード';

    const FAVORITE_GENRE ='好きなジャンル';

    const ISBN = 'ISBN';

    const TITLE = 'タイトル';

    const AUTHOR = '著者';

    const PUBLISHER = '出版社';

    const SERIES = 'シリーズ名';

    const GENRE = 'ジャンル';

    const YEAR_AND_MONTH = '出版年月';

    const YEAR = '出版年';

    const MONTH = '出版月';

    const IMAGE = '表紙の画像';

    const RECOMMENDATION = '推薦文';

    const BOOK_ID = 'book_id';

    const WANT_TO_READ_BOOK_ID = 'want_to_read_book_id';

    const RECOMMENDATION_ID = 'recommendation_id';

    const ALL_SEARCH = 'all';

    const AUTHOR_SEARCH = 'author' ;

    const SERIES_SEARCH = 'series';

    const TOKEN = 'token';

    /**項目名をキーとしたデータベースのカラム名の配列 */
    const COLUMN_NAMES = [
        self::NAME => Users::NAME,
        self::NICKNAME => Users::NICKNAME,
        self::PASSWORD => Users::PASSWORD,
        self::FAVORITE_GENRE => Users::GENRE_ID,
        self::TITLE => Books::TITLE,
        self::AUTHOR => Books::AUTHOR,
        self::PUBLISHER => Books::PUBLISHER,
        self::YEAR => Books::YEAR,
        self::MONTH => Books::MONTH,
        self::SERIES => Books::SERIES_TITLE,
        self::GENRE => Books::GENRE_ID,
        self::ISBN => Books::ISBN,
        self::IMAGE => Books::IMAGE,
        self::RECOMMENDATION => Recommendations::RECOMMENDATION,
    ];
    
    /**項目名をキーとした上限文字数の配列 */
    const MAX_LENGTH_ARRAY = [
        self::NAME => 256,
        self::NICKNAME => 30,
        self::TITLE => 240,
        self::AUTHOR => 120,
        self::PUBLISHER => 120,
        self::SERIES => 100,
        self::RECOMMENDATION => 500
    ];
    
    /**カラム名をキーとした入力値の配列 */
    protected $inputs;

    /**入力フォームのurl、セッションに保存するときのキーとしてつかう */
    protected $url;

    /**
     * コンストラクタ
     * 
     * @param string $url
     */
    public function __construct($url = '')
    {
        $this->url = $url;
    }
    

    /**
     * inputsを返却
     * 
     * @return array
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**セッションに入力内容保存 */
    public function saveHistories()
    {
        $_SESSION[Session::HISTORY][$this->url] = $this->inputs;
    }

    /**
     * セッションのunconfirmedに$inputsを保存
     */
    public function saveUnconfirmedInputs()
    {
        foreach($this->inputs as $k => $v) {
            Session::saveUnconfirmedData($this->url, $k, $v);
        }
        
    }

    /**
     * 指定の変数がgetで送信されてきてかつ1以上の自然数であるときその値を返却
     * 
     * @param string $name 取得する変数の名前
     * @return int|string
     */
    public static function filterIdGet($name)
    {
        $options = ['options'=>
                        ['min_range'=>1]
                        ];
        $id = filter_input(INPUT_GET, $name, FILTER_VALIDATE_INT, $options);

        if($id === null || $id === false) {
            $id = '';
        }

        return $id;
    }

    /**
     * 指定の変数がgetで送信されてきているときサニタイズ
     * 
     * @param string $name 取得する変数の名前
     * @return string
     */
    public static function sanitizeGet($name)
    {
        $str = filter_input(INPUT_GET, $name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if($str === null || $str === false)  {
            return '';
        }

        return $str;
    }
    
    /**
     * 指定の変数がpostで送信されてきているときそれを$inputsに追加した上で返却
     * 
     * @param string $name 取得する変数の名前
     * @return string
     */
    public function filterPost($name)
    {
        $str = filter_input(INPUT_POST, $name);
        if($str === null || $str === false) {
            $str = '';
        }

        $this->inputs[$name] = $str;

        return $str;
    }

    /**
     * 指定の変数がpostで送信されてきているときそれをサニタイズして$inputsに追加した上で返却
     * 
     * @param string $name 取得する変数の名前
     * @return string
     */
    public function sanitizePost($name)
    {
        $str = filter_input(INPUT_POST, $name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        if($str === null || $str === false) {
            $str = '';
        }

        $this->inputs[$name] = $str;

        return $str;
    }

    /**
     * 指定の変数がpostで送信されてきてかつ0を含む自然数であるときその値を$inputsに追加した上で返却
     * 
     * @param string $name 取得する変数の名前
     * @return int|string
     */
    public function filterNaturalNumberPost($name)
    {
        $options = ['options'=>
                        ['min_range'=>0]
                        ];
        $num = filter_input(INPUT_POST, $name, FILTER_VALIDATE_INT, $options);

        if($num === null || $num === false) {
            $num = '';
        }

        $this->inputs[$name] = $num;

        return $num;
    }
    
    /**
     * 指定の変数がpostで送信されてきてかつ1以上の自然数であるときその値を$inputsに追加した上で返却
     * 
     * @param string $name 取得する変数の名前
     * @return int|string
     */
    public function filterIdPost($name)
    {
        $options = ['options'=>
                        ['min_range'=>1]
                        ];
        $id = filter_input(INPUT_POST, $name, FILTER_VALIDATE_INT, $options);

        if($id === null || $id === false) {
            $id = '';
        }

        $this->inputs[$name] = $id;

        return $id;
    }
    
    /**
     * 指定の変数がpostで送信されてきてかつ1~12の自然数であるときその値を$inputsに追加した上で返却
     * 
     * @param string $name 取得する変数の名前
     * @return int|string
     */
    public function filterMonth($name)
    {
        $options = ['options'=>
                        ['min_range'=>1,
                        'max_range'=>12]
                        ];
        $month =  filter_input(INPUT_POST, $name, FILTER_VALIDATE_INT, $options);

        if($month === null || $month === false) {
            $month = '';
        }

        $this->inputs[$name] = $month;

        return $month;
    }
    
}