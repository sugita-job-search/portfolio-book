<?php
require_once('../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//セッションから不要なデータ削除
Session::deleteFormData();

//ワンタイムトークン生成
$token = Session::generateToken();

//サニタイズ
$all = Input::sanitizeGet(Input::ALL_SEARCH);
$author = Input::sanitizeGet(Input::AUTHOR_SEARCH);
$series = Input::sanitizeGet(Input::SERIES_SEARCH);

try {
    //検索ワードがないとき
    if ($all == '' && $author == '' && $series == '') {
        $cards = [];
        $value = '';
    } else {
        $db = new Books();

        //検索項目がauthor、seriesいずれかのときはその項目から検索
        if ($all === '' && $author !== '' && $series === '') {
            $cards = $db->searchBooksByAuthorOrSeries(Books::AUTHOR, $author);
            $value = $author;
        } elseif ($all === '' && $author === '' && $series !== '') {
            $cards = $db->searchBooksByAuthorOrSeries(Books::SERIES_TITLE, $series);
            $value = $series;
        } else {
            //それ以外のときは書名著者シリーズ名、isbnに一致するときはisbnからも検索
            $cards = $db->searchBooks($all);
            $value = $all;
        }
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
    <title>検索結果</title>
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
                <input type="text" name="all" class="form-control form-control-sm top-input" id="search" placeholder="書名や著者名で検索" aria-label="検索" value="<?= $value ?>">
                <button type="submit" class="btn btn-primary btn-sm">検索</button>
            </form>

            <div class="row">
                <div class="col-lg-9">
                    <h2>検索結果</h2>
                    <?php if (empty($cards)) : ?>
                        <div class="alert alert-dark mt-5" role="alert">
                            <div class="mb-3">
                                検索ワードに一致する本は見つかりませんでした
                            </div>
                            <a href="<?= Common::URL_ISBN_INPUT ?>" class="alert-link">新しい本を登録</a><br>
                            <a href="<?= Common::URL_TOP ?>" class="alert-link">トップページに戻る</a>
                        </div>
                    <?php else : ?>
                        <div class="row row-cols-auto justify-content-end">
                            <div class="col">
                                <p>
                                    お探しの本が見つからない方はこちら：
                                </p>
                            </div>
                            <div class="col">
                                <a href="<?= Common::URL_ISBN_INPUT ?>">新しい本を登録</a>
                            </div>
                        </div>

                        <?php foreach ($cards as $c) : ?>
                            <div class="card my-3 book-card" id="">
                                <div class="card-header">
                                    <a href="<?= Common::URL_BOOK . '?' . Input::BOOK_ID . '=' . $c[Books::ID] ?>" class="link-dark"><?= $c[Books::TITLE] ?></a>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <a href="<?= Common::URL_BOOK . '?' . Input::BOOK_ID . '=' . $c[Books::ID] ?>">
                                                <img src="../img/<?= Common::returnImageName($c[Books::IMAGE]) ?>" alt="" class="img-fluid">
                                            </a>
                                        </div>
                                        <div class="col-md-10">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <th>著者</th>
                                                    <td>
                                                        <div class="row row-cols-auto">
                                                            <?php foreach (explode("\n", $c[Books::AUTHOR]) as $a) : ?>
                                                                <div class="col">
                                                                    <a href="<?= Common::URL_SEARCH . '?' . Input::AUTHOR_SEARCH . '=' . $a ?>" class="link-dark"><?= $a ?></a>
                                                                </div>
                                                            <?php endforeach ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>出版社</th>
                                                    <td><?= $c[Books::PUBLISHER] ?></td>
                                                </tr>
                                                <tr>
                                                    <th>出版年月</th>
                                                    <td><?= $c[Books::YEAR] ?>年<?= $c[Books::MONTH] ?>月</td>
                                                </tr>
                                                <?php if ($c[Books::SERIES_TITLE] !== '') : ?>
                                                    <tr>
                                                        <th>シリーズ名</th>
                                                        <td>
                                                            <a href="<?= Common::URL_SEARCH . '?' . Input::SERIES_SEARCH . '=' . $c[Books::SERIES_TITLE] ?>" class="link-dark"><?= $c[Books::SERIES_TITLE] ?></a>
                                                        </td>
                                                    </tr>
                                                <?php endif ?>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <form action="<?= Common::URL_RECOMMENDATION_REGISTRATION ?>" method="get" class="col-sm-4 d-grid mb-1">
                                            <input type="hidden" name="<?= Input::BOOK_ID ?>" value="<?= $c[Books::ID] ?>">
                                            <button type="submit" class="btn btn-info">この本の推薦文を書く</button>
                                        </form>
                                        <form action="<?= Common::URL_BOOK ?>" method="get" class="col-sm-4 d-grid mb-1">
                                            <input type="hidden" name="<?= Input::BOOK_ID ?>" value="<?= $c[Books::ID] ?>">
                                            <button type="submit" class="btn btn-success">この本の推薦文を見る</button>
                                        </form>
                                        <?php if ($c['want'] == null) : ?>
                                            <form action="<?= Common::URL_WANT_TO_READ_REGISTRATION ?>" method="post" class="col-sm-4 d-grid mb-1">
                                                <input type="hidden" name="<?= Input::BOOK_ID ?>" value="<?= $c[Books::ID] ?>">
                                                <input type="hidden" name="<?= Input::TOKEN ?>" value="<?= $token ?>">
                                                <button type="submit" class="btn btn-warning">この本を読みたい本に追加</button>
                                            </form>
                                        <?php else : ?>
                                            <div class="col-sm-4 d-grid mb-1">
                                                <button type="button" class="btn btn-dark" disabled>読みたい本に追加済み</button>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>

                        <div class="float-end">
                            <a class="btn btn-outline-secondary" href="<?= Common::URL_TOP ?>" role="button">トップページに戻る</a>
                        </div>
                    <?php endif ?>
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