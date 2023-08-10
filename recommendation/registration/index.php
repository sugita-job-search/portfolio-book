<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//1以上の自然数が送られてこなかった場合はリダイレクト
$id = Input::filterIdGet(Input::BOOK_ID);

if ($id == '') {
    Common::topRedirect();
}

//セッションにエラーメッセージと入力履歴が保存されているとき取得
$display = new Display(Common::URL_RECOMMENDATION_REGISTRATION);

Session::deleteFormData();

//ワンタイムトークン生成
$token = Session::generateToken();

//データベースから本の情報を取得、情報がなかった場合はリダイレクト
try {
    $db = new Books();
    $book = $db->getBookGenreByBookId($id);
    if (!$book) {
        Common::redirect(Common::URL_RECOMMENDATION_SEARCH);
    }

    //著者を一人ずつに分割
    $authors = explode("\n", $book[Books::AUTHOR]);

    //書影がない場合はno imageの画像を表示
    if ($book[Books::IMAGE] == '') {
        $image = Common::NAME_NO_IMAGE;
    } else {
        $image = $book[Books::IMAGE];
    }
} catch (Exception $e) {
    Common::errorRedirect();
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>推薦文投稿</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a href="<?= Common::URL_TOP ?>" class="navbar-brand">LaraBook</a>
            </div>
        </nav>
    </header>
    <div class="container">
        <main>
            <form action="<?= Common::URL_SEARCH ?>" method="get" class="d-flex justify-content-end top-search">
                <input type="text" name="all" class="form-control form-control-sm top-input" id="search" placeholder="書名や著者名で検索" aria-label="検索">
                <button type="submit" class="btn btn-primary btn-sm">検索</button>
            </form>

            <div class="row">
                <div class="col-lg-9">
                    <div class="row mb-3">
                        <div class="col-sm-3 mb-2">
                            <img src="../../img/<?= $image ?>" alt="" class="img-fluid">
                        </div>
                        <div class="col-sm-8 mx-2">
                            <h3><?= $book[Books::TITLE] ?></h3>
                            <table class="table table-borderless">
                                <tr>
                                    <th>著者</th>
                                    <td>
                                        <div class="row row-cols-auto">
                                            <?php foreach ($authors as $a) : ?>
                                                <div class="col">
                                                    <?= $a ?>
                                                </div>
                                            <?php endforeach ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>出版社</th>
                                    <td><?= $book[Books::PUBLISHER] ?></td>
                                </tr>
                                <tr>
                                    <th>出版年月</th>
                                    <td><?= $book[Books::YEAR] ?>年<?= $book[Books::MONTH] ?>月</td>
                                </tr>
                                <tr>
                                    <th>ISBN</th>
                                    <td><?= $book[Books::ISBN] ?></td>
                                </tr>
                                <?php if ($book[Books::SERIES_TITLE] !== '') : ?>
                                    <tr>
                                        <th>シリーズ名</th>
                                        <td><?= $book[Books::SERIES_TITLE] ?></td>
                                    </tr>
                                <?php endif ?>
                                <tr>
                                    <th>ジャンル</th>
                                    <td><?= $book[Genres::GENRE] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h2>推薦文投稿</h2>
                    <form action="./confirm.php ?>" method="post" id="form">
                        <p>推薦文を入力してください（500文字以内）</p>
                        <div class="mb-3">
                            <textarea name="recommendation" class="form-control" rows="5" aria-label="推薦文"><?= $display->getHistory(Recommendations::RECOMMENDATION) ?></textarea>
                            <?= $display->getMessage(Input::RECOMMENDATION) ?>
                            <input type="hidden" name="<?= Recommendations::BOOK_ID ?>" value="<?= $id ?>">
                            <input type="hidden" name="<?= Input::TOKEN ?>" value="<?= $token ?>">
                        </div>
                        <div class="d-grid gap-2 d-md-block">
                            <button type="submit" class="btn btn-primary mx-2">入力内容の確認</button>
                            <a class="btn btn-outline-secondary mx-2" href="<?= Common::URL_TOP ?>" role="button">トップページに戻る</a>
                        </div>
                    </form>
                </div>
                <div class="col-lg-3">
                    <div class="card float-lg-end side-menu">
                        <div class="card-header">
                            <?= $_SESSION[Session::USER][Users::NICKNAME] ?>さん
                        </div>
                        <div class="list-group">
                            <a href="<?= Common::URL_RECOMMENDATION_SEARCH ?>" class="list-group-item list-group-item-action">新しい推薦文を書く</a>
                            <a href="<?= Common::URL_WANT_TO_READ ?>" class="list-group-item list-group-item-action">読みたい本</a>
                            <a href="<?= Common::URL_RECOMMENDATION ?>" class="list-group-item list-group-item-action">あなたの推薦文</a>
                            <a href="<?= Common::URL_MEMBER ?>" class="list-group-item list-group-item-action">会員情報確認</a>
                            <a href="<?= Common::URL_LOGOUT ?>" class="list-group-item list-group-item-action">ログアウト</a>
                        </div>
                    </div>
                </div>
        </main>
        <footer>
            <p>&copy;Copyright sugita. All rights reserved</p>
        </footer>
    </div>
</body>

</html>