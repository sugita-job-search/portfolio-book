<?php

/**共通クラス */
class Common
{
    /**トップページのurl */
    const URL_TOP = 'http://localhost/book/';

    /**検索結果ページのurl */
    const URL_SEARCH = 'http://localhost/book/book/search.php';

    /**本の個別ページのurl */
    const URL_BOOK = 'http://localhost/book/book/';

    /**ログインページのurl */
    const URL_LOGIN = 'http://localhost/book/member/login/';

    /**会員登録ページのurl */
    const URL_MEMBER_REGISTRATION = 'http://localhost/book/member/registration/';

    /**会員情報確認ページのurl */
    const URL_MEMBER = 'http://localhost/book/member/';

    /**会員情報変更ページのurl */
    const URL_MEMBER_EDIT = 'http://localhost/book/member/edit/';

    /**ISBN入力ページのurl */
    const URL_ISBN_INPUT = 'http://localhost/book/book/registration/isbn.php';

    /**書誌情報入力ページのurl */
    const URL_BOOK_REGISTRATION = 'http://localhost/book/book/registration/';

    /**書誌重複登録防止ページのurl */
    const URL_ISBN_DUPLICATION = 'http://localhost/book/book/registration/error.php';

    /**書誌情報編集ページのurl */
    const URL_BOOK_EDIT = 'http://localhost/book/book/edit/';

    /**自分の推薦文一覧のurl */
    const URL_RECOMMENDATION = 'http://localhost/book/recommendation/';
    
    /**推薦文を書く本を検索するページのurl */
    const URL_RECOMMENDATION_SEARCH ='http://localhost/book/recommendation/registration/search.php';

    /**推薦文入力ページのurl */
    const URL_RECOMMENDATION_REGISTRATION = 'http://localhost/book/recommendation/registration/';

    /**推薦文編集ページのurl */
    const URL_RECOMMENDATION_EDIT = 'http://localhost/book/recommendation/edit/';

    /**推薦文削除確認ページのurl */
    const URL_RECOMMENDATION_DELETE = 'http://localhost/book/recommendation/delete/';

    /**読みたい本一覧のurl */
    const URL_WANT_TO_READ = 'http://localhost/book/want-to-read/';

    /**読みたい本に追加のurl */
    const URL_WANT_TO_READ_REGISTRATION = 'http://localhost/book/want-to-read/registration/process.php';

    /**読みたい本削除確認ページのurl */
    const URL_WANT_TO_READ_DELETE = 'http://localhost/book/want-to-read/delete/';

    /**ログアウトのurl */
    const URL_LOGOUT = 'http://localhost/book/member/logout/process.php';

    /**no imageの画像の名前 */
    const NAME_NO_IMAGE = 'no_image_tate.jpg';


    /**トップページにリダイレクト */
    public static function topRedirect()
    {
        header('Location: http://localhost/book/');
        exit;
    }

    /**エラーページへリダイレクト */
    public static function errorRedirect()
    {
        header('Location: http://localhost/book/error.html');
        exit;
    }

    /**
     * 指定ページにリダイレクト
     * 
     * @param string $url　リダイレクト先のurl
     */
    public static function redirect($url)
    {
        $header = 'Location:' . $url;
        header($header);
        exit;
    }

    /**
     * 空文字を渡されたときはno image画像の名前、それ以外のときは渡された文字列を返却
     * 
     * @param string $image_name
     * @return string
     */
    public static function returnImageName($image_name)
    {
        if($image_name == '') {
            return self::NAME_NO_IMAGE;
          } else {
            return $image_name;
          }
    }

    /**
     * isbn-10をisbn-13に変換
     * 
     * @param string $isbn
     * @return string
     */
    public static function convertIsbn10To13($isbn)
    {
        //末尾1文字以外を取り出す
        $str = substr($isbn, 0, -1);

        //頭に978をつける
        $str = '978' . $str;

        //チェックディジット計算
        $odd_sum = 0;
        $even_sum = 0;
        for ($i = 0; $i < 12; $i++) {
            if (($i % 2) == 0) {
                $odd_sum += $str[$i];
            } else {
                $even_sum += $str[$i];
            }
        }

        $sum = $odd_sum + $even_sum * 3;

        $check = 10 - $sum % 10;

        //チェックディジットをつけて返却
        if ($check == 10) {
            return $str . '0';
        }

        return $str . $check;
    }

    /**
     * isbn-13をisbn-10に変換
     * 
     * @param string $isbn
     * @return string
     */
    public static function convertIsbn13To10($isbn)
    {
        //4桁目から12桁目を取り出す
        $str = substr($isbn, 3, 9);

        //チェックディジット計算
        $sum = 0;
        for ($i = 0, $j = 10; $i < 9; $i++, $j--) {
            $sum += $str[$i] * $j;
        }

        $check = 11 - $sum % 11;

        //チェックディジットをつけて返却
        if ($check == 10) {
            return $str . 'X';
        }

        if ($check == 11) {
            return $str . '0';
        }

        return $str . $check;
    }

    /**
     * 文字列の全角英数字を半角にしてハイフンを削除した上で、isbnの形式に合致していればisbn-13形式にして返す
     * 
     * @param string $str isbnかもしれない文字列
     * @return string isbnまたは空文字
     */
    public static function validateIsbn($str)
    {
        //全角英数字を半角に変換
        $str = mb_convert_kana($str, 'a');

        //ハイフンを削除
        $str = str_replace(['-', 'ー'], '', $str);

        //末尾1文字とそれ以外に分離
        $digits = mb_substr($str, 0, -1);
        $end = mb_substr($str, -1);

        //末尾以外に数字以外の文字があったら空文字返却
        if(!ctype_digit($digits)) {
            return '';
        }

        //末尾含めて10桁のときはチェックディジットが合っていればisbn-13に変換して返却
        if(strlen($digits) == 9) {
            $sum = 0;
            for($i = 0, $j = 10; $i < 9; $i ++, $j --) {
                $sum += $digits[$i] * $j;
            }

            $check = 11 - $sum % 11;
            
            if($check == $end) {
                return Common::convertIsbn10To13($str);
            }
            
            if($check == 11 && $end == 0) {
                return Common::convertIsbn10To13($str);
            }
            
            if($check == 10) {
                if($end == 'X' || $end = 'x') {
                    return Common::convertIsbn10To13($str);
                }
            }
        }

        //末尾含めて13桁のときはチェックディジットが合っていれば返却
        if(strlen($digits) == 12) {
            $odd_sum = 0;
            $even_sum = 0;
            for($i = 0; $i < 12; $i ++) {
                if(($i % 2) == 0) {
                    $odd_sum += $digits[$i];
                } else {
                    $even_sum += $digits[$i];
                }
            }

            $sum = $odd_sum + $even_sum * 3;

            $check = 10 - $sum % 10;

            if($check == 10 && $end == 0) {
                return $str;
            }

            if($check == $end) {
                return $str;
            }
        }

        //10桁でも13桁でもないときとチェックディジットが合わなかったときは空文字返却
        return '';
    }
}
