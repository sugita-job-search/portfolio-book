<?php
require_once('../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//セッションから不要なデータ削除
Session::deleteFormData();

//推薦文と本の情報取得
try {
    $db = new Recommendations();

    $recommendations = $db->getRecommendationsByUser();
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
    <title>あなたの推薦文</title>
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
                    <h2>あなたの推薦文</h2>

                    <?php if (empty($recommendations)) : ?>
                        <div class="alert alert-dark mt-5" role="alert">
                            <div class="my-3">
                                推薦文はまだありません
                            </div>
                        </div>
                    <?php else : ?>
                        <?php foreach ($recommendations as $r) : ?>
                            <div class="card my-3 book-recommend-card" id="<?= $r[Recommendations::ID] ?>">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <a href="<?= Common::URL_BOOK . '?' . Input::BOOK_ID . '=' . $r[Recommendations::BOOK_ID] ?> ?>">
                                                <img src="../img/<?= Common::returnImageName($r[Books::IMAGE]) ?>" alt="" class="img-fluid">
                                            </a>
                                        </div>
                                        <div class="col-md-11">
                                            <div class="book-title">
                                                <a href="<?= Common::URL_BOOK . '?' . Input::BOOK_ID . '=' . $r[Recommendations::BOOK_ID] ?>" class="link-dark"><?= $r[Books::TITLE] ?></a>
                                            </div>
                                            <div class="row row-cols-auto">
                                                <?php foreach (explode("\n", $r[Books::AUTHOR]) as $a) : ?>
                                                    <div class="col">
                                                        <a href="../book/search.php?<?= Input::AUTHOR_SEARCH . '=' . $a ?>" class="link-dark"><?= $a ?></a>
                                                    </div>
                                                <?php endforeach ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <p class="card-text">
                                        <?= nl2br($r[Recommendations::RECOMMENDATION]) ?>
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <div class="row row-cols-md-auto justify-content-end">
                                        <form action="<?= Common::URL_RECOMMENDATION_EDIT ?>" method="get" class="col d-grid">
                                            <input type="hidden" name="<?= Input::RECOMMENDATION_ID ?>" value="<?= $r[Recommendations::ID] ?>">
                                            <button type="submit" class="btn btn-info btn-sm">編集</button>
                                        </form>
                                        <form action="<?= Common::URL_RECOMMENDATION_DELETE ?>" method="post" class="col d-grid">
                                            <input type="hidden" name="<?= Input::RECOMMENDATION_ID ?>" value="<?= $r[Recommendations::ID] ?>">
                                            <input type="hidden" name="<?= Input::TOKEN ?>" value="<?= Session::generateToken() ?>">
                                            <button type="submit" class="btn btn-secondary btn-sm">削除</button>
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