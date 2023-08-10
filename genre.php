<?php
require_once('./app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//セッションから不要なデータ削除
Session::deleteFormData();

try {
    $db = new Genres();

    $genres = $db->getGenres();
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
    <title>表示ジャンル選択</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />
    <link rel="stylesheet" href="./css/style.css">
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
                    <h2>表示ジャンル選択</h2>
                    <form action="<?= Common::URL_TOP ?>" method="get" class="mx-1 my-3">
                        <div class="row">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="genres[]" value="0" id="all-genre">
                                <label class="form-check-label" for="all-genre">
                                    すべて
                                </label>
                            </div>
                        </div>
                        <div class="row row-cols-1 row-cols-md-3">
                            <?php foreach ($genres as $key => $genre) : ?>
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="genres[]" value="<?= $key ?>" id="<?= $genre ?>">
                                        <label class="form-check-label" for="<?= $genre ?>">
                                            <?= $genre ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach ?>

                        </div>
                        <div class="d-grid gap-2 d-md-block mt-3">
                            <button type="submit" class="btn btn-primary mx-2">決定</button>
                            <a class="btn btn-outline-secondary mx-2" href="<?= Common::URL_TOP ?>l" role="button">トップページに戻る</a>
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