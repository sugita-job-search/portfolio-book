<?php
require_once('./app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//セッションから不要なデータ削除
Session::deleteFormData();

//ワンタイムトークン生成
$token = Session::generateToken();

//表示ジャンルがgetで送られてきているとき取得、1以上の整数でない要素はfalseに置き換わる
$options = [
    'flags' => FILTER_REQUIRE_ARRAY,
    'options' => ['min_range' => 1]
];

$genre_ids = filter_input(INPUT_GET, 'genres', FILTER_VALIDATE_INT, $options);

try {
    //表示ジャンルが取得されなかったときはログイン中のユーザーの好きなジャンル取得
    if ($genre_ids === null || $genre_ids === false || $genre_ids === []) {
        $db = new Users();
        $id_and_genre = $db->getGenreByUserId($_SESSION[Session::USER][Users::ID]);
        $genre_ids = [$id_and_genre[Users::GENRE_ID]];
        $genres = [$id_and_genre[Genres::GENRE]];
        $count = 1;
    } else {
        //ジャンル選択画面ですべてが選択されたときと整数でない値が送られてきたときは全ジャンルの推薦文を取得
        if (in_array(false, $genre_ids)) {
            $genre_ids = [0];
        } else {
            //配列が1以上の整数のみで構成されているとき、配列から重複している要素を削除
            $genre_ids = array_unique($genre_ids);

            //配列の要素数カウント
            $count = count($genre_ids);

            //ジャンル名取得
            $db = new Genres();
            $genres = $db->getGenresByGenreId($genre_ids, $count);

            //取得できなかったとき全てのジャンルを表示
            if (empty($genres)) {
                $genre_ids = [0];
            }
        }
    }

    $db = new Recommendations();

    //全ジャンルを表示するとき
    if ($genre_ids == [0]) {
        $genres = ['すべて'];
        $cards = $db->getAllRecommendations();
        //特定ジャンルだけ表示するとき
    } else {
        $cards = $db->getRecommendationsByGenres($genre_ids, $count);
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
    <title>LaraBook</title>
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
                    <h2>みんなの推薦文</h2>
                    <div class="row row-cols-auto justify-content-end">
                        <div class="col">
                            <p>
                                表示中のジャンル：
                            </p>
                        </div>
                        <ul class="col genre-list">
                            <div class="row row-cols-auto justify-content-end">
                                <?php foreach ($genres as $g) : ?>
                                    <div class="col">
                                        <li><?= $g ?></li>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </ul>
                        <div class="col">
                            <a href="./genre.php">変更</a>
                        </div>
                    </div>

                    <?php if (empty($cards)) : ?>
                        <div class="alert alert-dark mt-5" role="alert">
                            <div class="mb-3">
                                このジャンルの本の推薦文はまだ投稿されていません
                            </div>
                            <a href="<?= Common::URL_RECOMMENDATION_SEARCH ?>" class="alert-link">推薦文を投稿</a>
                        </div>

                    <?php else : ?>
                        <?php foreach ($cards as $c) : ?>
                            <div class="card my-3 book-recommend-card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <a href="./book/?<?= Input::BOOK_ID . '=' . $c[Recommendations::BOOK_ID] ?>">
                                                <img src="./img/<?= Common::returnImageName($c[Books::IMAGE]) ?>" alt="" class="img-fluid">
                                            </a>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="book-title">
                                                <a href="./book/?<?= Input::BOOK_ID . '=' . $c[Recommendations::BOOK_ID] ?>" class="link-dark"><?= $c[Books::TITLE] ?></a>
                                            </div>
                                            <div class="row row-cols-auto">
                                                <?php foreach (explode("\n", $c[Books::AUTHOR]) as $a) : ?>
                                                    <div class="col">
                                                        <a href="./book/search.php?<?= Input::AUTHOR_SEARCH . '=' . $a ?>" class="link-dark"><?= $a ?></a>
                                                    </div>
                                                <?php endforeach ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <?php if ($c['want'] != null) : ?>
                                                <div class="d-grid">
                                                    <button type="button" class="btn btn-dark btn-sm" disabled>読みたい本に追加済み</button>
                                                </div>
                                            <?php else : ?>
                                                <form action="./want-to-read/registration/process.php" method="post" class="d-grid">
                                                    <input type="hidden" name="<?= WantToReadBooks::BOOK_ID ?>" value="<?= $c[Recommendations::BOOK_ID] ?>">
                                                    <input type="hidden" name="<?= Input::TOKEN ?>" value="<?= $token ?>">
                                                    <button type="submit" class="btn btn-warning btn-sm">読みたい本に追加</button>
                                                </form>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <p class="card-text">
                                        <?= nl2br($c[Recommendations::RECOMMENDATION]) ?>
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <?= $c[Users::NICKNAME] ?>
                                </div>
                            </div>
                        <?php endforeach ?>
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
            </div>
        </main>
        <footer>
            <p>&copy;Copyright sugita. All rights reserved</p>
        </footer>
    </div>
</body>

</html>