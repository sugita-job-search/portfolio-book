<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//セッションから本のidと推薦文取得、取得できなければリダイレクト
$data = Session::getUnconfirmedDataArray(Common::URL_RECOMMENDATION_REGISTRATION);

if ($data == []) {
    Common::topRedirect();
}

Session::deleteFormData();

//本の情報取得
try {
    $db = new Books();
    $book = $db->getTitleAuthorImage($data[Recommendations::BOOK_ID]);

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
    <title>推薦文投稿完了</title>
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
                    <h2>推薦文投稿完了</h2>
                    <p>推薦文が投稿できました！</p>
                    <div class="card my-3 book-recommend-card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-1">
                                    <img src="../../img/<?= $image ?>" alt="" class="img-fluid">
                                </div>
                                <div class="col-md-11">
                                    <div class="book-title">
                                        <?= $book[Books::TITLE] ?>
                                    </div>
                                    <div class="row row-cols-auto">
                                        <?php foreach ($authors as $a) : ?>
                                            <div class="col">
                                                <?= $a ?>
                                            </div>
                                        <?php endforeach ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <p class="card-text">
                                <?= $data[Recommendations::RECOMMENDATION] ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <?= $_SESSION[Session::USER][Users::NICKNAME] ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 d-grid mb-1">
                            <a class="btn btn-info" href="<?= Common::URL_RECOMMENDATION_SEARCH ?>" role="button">違う本の推薦文を書く</a>
                        </div>
                        <div class="col-sm-4 d-grid mb-1">
                            <a class="btn btn-success" href="<?= Common::URL_BOOK . '?' . Input::BOOK_ID . '=' . $data[Recommendations::BOOK_ID] ?>" role="button">他の人の推薦文を見る</a>
                        </div>
                        <div class="col-sm-4 d-grid mb-1">
                            <a class="btn btn-outline-secondary" href="<?= Common::URL_TOP ?>" role="button">トップページに戻る</a>
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