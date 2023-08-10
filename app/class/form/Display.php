<?php
/**フォームに表示するエラーメッセージと入力履歴を操作するクラス */
class Display {
    
    /**エラーメッセージが代入される変数 */
    private $messages;
    
    /**入力履歴が代入される変数 */
    private $histories;

    /**
     * コンストラクタ
     * 
     * @param string $url 入力フォームのurl
     */
    public function __construct($url)
    {   
        if(isset($_SESSION[Session::MESSAGE][$url])) {
            $this->messages = $_SESSION[Session::MESSAGE][$url];
        } else {
            $this->messages = [];
        }

        if(isset($_SESSION[Session::HISTORY][$url])) {
            $this->histories = $_SESSION[Session::HISTORY][$url];
        } else {
            $this->histories = [];
        }
    }

    /**
     * 指定項目のエラーメッセージがあればその部分のHTMLを返却
     * 
     * @param string $item_name 項目名
     * @return string
     */
    public function getMessage($item_name)
    {
        if(isset($this->messages[$item_name])) {
            $message = '<div class="error-message">';
            $message .= $this->messages[$item_name];
            $message .= '</div>';
            
            return $message;
        }

        return '';
    }

    /**
     * 指定項目の入力履歴があればそれを返却
     * 
     * @param string $column_name カラム名
     * @return string
     */
    public function getHistory($column_name)
    {
        if(isset($this->histories[$column_name])) {
            return $this->histories[$column_name];
        }

        return '';
    }

    /**
     * 入力履歴を全て返却
     * 
     * @return array
     */
    public function getHistories()
    {
        return $this->histories;
    }
}