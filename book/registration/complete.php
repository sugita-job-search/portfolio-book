<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//セッションのunconfirmedから本のid取得
$id = Session::getUnconfirmedData(Common::URL_BOOK_REGISTRATION, Books::ID);
Session::deleteFormData();

//データがなかったらリダイレクト
if ($id == '') {
    Common::redirect(Common::URL_ISBN_INPUT);
}

//データベースから本の情報を取得
try {
    $db = new Books();
    $book = $db->getBookGenreByBookId($id);

    if (empty($book)) {
        Common::errorRedirect();
    }

    //著者を一人ずつに分割
    $authors = explode("\n", $book[Books::AUTHOR]);

    //書影がない場合はno imageの画像を表示
    $image = Common::returnImageName($book[Books::IMAGE]);
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
    <title>登録完了</title>
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
                    <div class="alert alert-info" role="alert">
                        <h2 class="alert-heading">登録完了</h2>
                        <p>
                            新しい本が登録できました！<br>
                            ぜひ推薦文を投稿してください！
                        </p>
                    </div>
                    <div class="row">
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
                                            <?php foreach ($authors as $author) : ?>
                                                <div class="col">
                                                    <?= $author ?>
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
                                    <th>ISBN-10</th>
                                    <td><?= Common::convertIsbn13To10($book[Books::ISBN]) ?></td>
                                </tr>
                                <tr>
                                    <th>ISBN-13</th>
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
                        <div class="d-grid gap-2 d-md-block">
                            <a class="btn btn-info mx-2" href="<?= Common::URL_RECOMMENDATION_REGISTRATION . '?' . Input::BOOK_ID . '=' . $book[Books::ID] ?>" role="button">この本の推薦文を書く</a>
                            <a class="btn btn-outline-secondary mx-2" href="<?= Common::URL_TOP ?>" role="button">トップページに戻る</a>
                        </div>
                    </div>
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