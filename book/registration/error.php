<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//セッションから不要なデータ削除
Session::deleteFormData();

//idがgetで送られてきているとき取得
$id = Input::filterIdGet(Input::BOOK_ID);

//取得できなかったときリダイレクト
if ($id == '') {
    Common::redirect(Common::URL_ISBN_INPUT);
}

//データベースから本の情報を取得
try {
    $db = new Books();

    $book = $db->getBookGenreWantByBookId($id);

    if ($book == false) {
        Common::redirect(Common::URL_ISBN_INPUT);
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
    <title>登録エラー</title>
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
                    <h2>登録エラー</h2>
                    <p>この本はすでに登録されています</p>

                    <div class="card my-3 book-card">
                        <div class="card-header">
                            <a href="<?= Common::URL_BOOK ?>?book-id=<?= $book[Books::ID] ?>" class="link-dark"><?= $book[Books::TITLE] ?></a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <a href="<?= Common::URL_BOOK ?>?book-id=<?= $book[Books::ID] ?>">
                                        <img src="../../img/<?= $image ?>" alt="" class="img-fluid">
                                    </a>
                                </div>
                                <div class="col-md-10">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>著者</th>
                                            <td>
                                                <div class="row row-cols-auto">
                                                    <?php foreach ($authors as $a) : ?>
                                                        <div class="col">
                                                            <a href="<?= Common::URL_SEARCH ?>?author=<?= $a ?>" class="link-dark"><?= $a ?></a>
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
                                        <?php if ($book[Books::SERIES_TITLE] !== '') : ?>
                                            <tr>
                                                <th>シリーズ名</th>
                                                <td>
                                                    <a href="<?= Common::URL_SEARCH ?>?series=<?= $book[Books::SERIES_TITLE] ?>" class="link-dark"><?= $book[Books::SERIES_TITLE] ?></a>
                                                </td>
                                            </tr>
                                        <?php endif ?>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <form action="<?= Common::URL_RECOMMENDATION_REGISTRATION ?>" method="get" class="col-sm-4 d-grid mb-1">
                                    <input type="hidden" name="<?= Input::BOOK_ID ?>" value="<?= $book[Books::ID] ?>">
                                    <button type="submit" class="btn btn-info">この本の推薦文を書く</button>
                                </form>
                                <form action="<?= Common::URL_BOOK ?>" method="get" class="col-sm-4 d-grid mb-1">
                                    <input type="hidden" name="<?= Input::BOOK_ID ?>" value="<?= $book[Books::ID] ?>">
                                    <button type="submit" class="btn btn-success">この本の推薦文を見る</button>
                                </form>
                                <?php if ($book['want'] == null) : ?>
                                    <form action="<?= Common::URL_WANT_TO_READ_REGISTRATION ?>" method="post" class="col-sm-4 d-grid mb-1">
                                        <input type="hidden" name="<?= Input::BOOK_ID ?>" value="<?= $book[Books::ID] ?>">
                                        <button type="submit" class="btn btn-warning ">この本を読みたい本に追加</button>
                                    </form>
                                <?php else : ?>
                                    <div class="col-sm-4 d-grid mb-1">
                                        <button type="button" class="btn btn-dark" disabled>読みたい本に追加済み</button>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-block mt-3">
                        <a class="btn btn-secondary mx-2" href="<?= Common::URL_ISBN_INPUT ?>" role="button">戻る</a>
                        <a class="btn btn-outline-secondary mx-2" href="<?= Common::URL_TOP ?>" role="button">トップページに戻る</a>
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