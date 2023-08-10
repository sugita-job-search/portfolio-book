<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//isbnを取得、入力されていないときisbn入力ページにリダイレクト
$isbn = Session::getUnconfirmedData(Common::URL_ISBN_INPUT, Books::ISBN);
if ($isbn == '') {
    Common::redirect(Common::URL_ISBN_INPUT);
}

//isbn以外の項目取得、なかったらリダイレクト
if (isset($_SESSION[Session::UNCONFIRMED][Common::URL_BOOK_REGISTRATION])) {
    $data = $_SESSION[Session::UNCONFIRMED][Common::URL_BOOK_REGISTRATION];
} else {
    Common::redirect(Common::URL_BOOK_REGISTRATION);
}

//ジャンルを取得
try {
    $db = new Genres();

    $genre = $db->getGenre($data[Books::GENRE_ID]);
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
    <title>登録内容確認</title>
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
                    <div class="row justify-content-center">
                        <div class="col-sm-9">
                            <h2>登録内容確認</h2>
                            <p>以下の内容で登録します</p>

                            <form action="./process.php" method="post" class="my-3">
                                <div class="mb-1">
                                    <label for="title" class="form-label">タイトル</label>
                                    <input type="text" name="title" readonly class="form-control-plaintext" id="title" value="<?= $data[Books::TITLE] ?>" />
                                </div>
                                <div class="mb-1">
                                    <label for="author" class="form-label">著者</label>
                                    <textarea readonly name="author" class="form-control-plaintext" id="author" rows="3"><?= $data[Books::AUTHOR] ?></textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="publisher" class="form-label">出版社</label>
                                    <input type="text" name="publisher" readonly class="form-control-plaintext" id="publisher" value="<?= $data[Books::PUBLISHER] ?>" />
                                </div>
                                <div class="mb-1">
                                    <label for="series" class="form-label">出版年月</label>
                                    <input type="text" name="year-month" readonly class="form-control-plaintext" id="year-month" value="<?= $data[Books::YEAR] ?>年 <?= $data[Books::MONTH] ?>月" />
                                </div>
                                <?php if ($data[Books::SERIES_TITLE] !== '') : ?>
                                    <div class="mb-1">
                                        <label for="series" class="form-label">シリーズ名</label>
                                        <input type="text" name="series" readonly class="form-control-plaintext" id="series" value="<?= $data[Books::SERIES_TITLE] ?>" />
                                    </div>
                                <?php endif ?>
                                <div class="mb-1">
                                    <label for="genre" class="form-label">ジャンル</label>
                                    <input type="text" name="genre" readonly class="form-control-plaintext" id="genre" value="<?= $genre ?>" />
                                </div>
                                <div class="mb-1">
                                    <label for="isbn" class="form-label">ISBN-10</label>
                                    <input type="text" name="isbn" readonly class="form-control-plaintext" id="isbn" value="<?= Common::convertIsbn13To10($isbn) ?>" />
                                </div>
                                <div class="mb-1">
                                    <label for="isbn" class="form-label">ISBN-13</label>
                                    <input type="text" name="isbn" readonly class="form-control-plaintext" id="isbn" value="<?= $isbn ?>" />
                                </div>
                                <?php if (isset($_SESSION[Session::UNCONFIRMED][Common::URL_BOOK_REGISTRATION][Session::FILE])) : ?>
                                    <div class="mb-1">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-label">表紙の画像</div>
                                                <img src="../../app/img.php" alt="" class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                <?php endif ?>
                                <input type="hidden" name="<?= Input::TOKEN ?>" value="<?= Session::generateToken() ?>">
                                <div class="d-grid gap-2 d-md-block mt-3">
                                    <button type="submit" class="btn btn-primary mx-2">登録</button>
                                    <a class="btn btn-secondary mx-2" href="./index.php" role="button">前の画面に戻る</a>
                                    <a class="btn btn-secondary mx-2" href="./isbn.php" role="button">ISBN入力に戻る</a>
                                </div>
                            </form>
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