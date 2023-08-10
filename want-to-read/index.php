<?php
require_once('../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//セッションから不要なデータ削除
Session::deleteFormData();

//ワンタイムトークン生成
$token = Session::generateToken();

//データベースから読みたい本取得
try {
    $db = new WantToReadBooks();
    $books = $db->getWantToReadBooksByUserId();

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
    <title>読みたい本</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/style.css">
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
                    <h2>あなたの読みたい本</h2>

                    <?php if (empty($books)) : ?>
                        <div class="alert alert-dark mt-5" role="alert">
                            <div class="my-3">
                                読みたい本はまだありません
                            </div>
                        </div>

                    <?php else : ?>
                        <?php foreach ($books as $b) : ?>
                            <div class="card my-3 book-card" id="<?= $b[WantToReadBooks::ID] ?>">
                                <div class="card-header">
                                    <a href="../book/?<?= Input::BOOK_ID . '=' . $b['book_id'] ?>" class="link-dark"><?= $b[Books::TITLE] ?></a>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <a href="../book/?<?= Input::BOOK_ID . '=' . $b['book_id'] ?>">
                                                <img src="../img/<?= Common::returnImageName($b[Books::IMAGE]) ?>" alt="" class="img-fluid">
                                            </a>
                                        </div>
                                        <div class="col-md-10">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <th>著者</th>
                                                    <td>
                                                        <div class="row row-cols-auto">
                                                            <?php foreach (explode("\n", $b[Books::AUTHOR]) as $a) : ?>
                                                                <div class="col">
                                                                    <a href="../book/search.php?<?= Input::AUTHOR_SEARCH . '=' . $a ?>" class="link-dark"><?= $a ?></a>
                                                                </div>
                                                            <?php endforeach ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>出版社</th>
                                                    <td><?= $b[Books::PUBLISHER] ?></td>
                                                </tr>
                                                <tr>
                                                    <th>出版年月</th>
                                                    <td><?= $b[Books::YEAR] ?>年<?= $b[Books::MONTH] ?>月</td>
                                                </tr>
                                                <?php if ($b[Books::SERIES_TITLE] !== '') : ?>
                                                    <tr>
                                                        <th>シリーズ名</th>
                                                        <td>
                                                            <a href="../book/search.php?<?= Input::SERIES_SEARCH . '=' . $b[Books::SERIES_TITLE] ?>" class="link-dark"><?= $b[Books::SERIES_TITLE] ?></a>
                                                        </td>
                                                    </tr>
                                                <?php endif ?>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <form action="<?= Common::URL_RECOMMENDATION_REGISTRATION ?>" method="get" class="col-sm-4 d-grid mb-1">
                                            <input type="hidden" name="<?= Input::BOOK_ID ?>" value="<?= $b[WantToReadBooks::BOOK_ID] ?>">
                                            <button type="submit" class="btn btn-info">この本の推薦文を書く</button>
                                        </form>
                                        <form action="<?= Common::URL_BOOK ?>" method="get" class="col-sm-4 d-grid mb-1">
                                            <input type="hidden" name="<?= Input::BOOK_ID ?>" value="<?= $b[WantToReadBooks::BOOK_ID] ?>">
                                            <button type="submit" class="btn btn-success">この本の推薦文を見る</button>
                                        </form>
                                        <form action="./delete/" method="post" class="col-sm-4 d-grid mb-1">
                                            <input type="hidden" name="<?= Input::WANT_TO_READ_BOOK_ID ?>" value="<?= $b[WantToReadBooks::ID] ?>">
                                            <input type="hidden" name="<?= Input::TOKEN ?>" value="<?= $token ?>">
                                            <button type="submit" class="btn btn-secondary">読みたい本から削除</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php endif ?>
                    <div class="float-end">
                        <a class="btn btn-outline-secondary" href="<?= Common::URL_TOP ?>" role="button">トップページに戻る</a>
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