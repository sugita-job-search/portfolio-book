<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//セッションから不要なデータ削除
Session::deleteFormData();

//ワンタイムトークン照合
Session::verifyToken();

//1以上の自然数が送られてこなかった場合はリダイレクト
$input = new Input(Common::URL_RECOMMENDATION_DELETE);

$id = $input->filterIdPost(Input::RECOMMENDATION_ID);

if ($id == '') {
    Common::redirect(Common::URL_RECOMMENDATION);
}

//推薦文と本の情報をデータベースから取得
try {
    $db = new Recommendations();

    $card = $db->getRecommendationBookByRecommendationId($id);

    //取得できなければリダイレクト
    if (!$card) {
        Common::redirect(Common::URL_RECOMMENDATION);
    }

    //著者を一人ずつに分割
    $authors = explode("\n", $card[Books::AUTHOR]);

    //書影がない場合はno imageの画像を表示
    $image = Common::returnImageName($card[Books::IMAGE]);

    //セッションunconfirmedに推薦文のid保存
    $input->saveUnconfirmedInputs();
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
    <title>削除確認</title>
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
                    <h2>推薦文削除確認</h2>
                    <p>以下の投稿を削除します</p>

                    <div class="card my-3 book-recommend-card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-1">
                                    <img src="../../img/<?= $image ?>" alt="" class="img-fluid">
                                </div>
                                <div class="col-md-11">
                                    <div class="book-title"><?= $card[Books::TITLE] ?></div>
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
                                <?= nl2br($card[Recommendations::RECOMMENDATION]) ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <?= $_SESSION[Session::USER][Users::NICKNAME] ?>
                        </div>
                    </div>

                    <form action="./process.php" method="post">
                        <input type="hidden" name="<?= Input::TOKEN ?>" value="<?= Session::generateToken() ?>">
                        <div class="d-grid gap-2 d-md-block">
                            <button type="submit" class="btn btn-secondary mx-2">削除</button>
                            <a class="btn btn-outline-secondary mx-2" href="<?= Common::URL_RECOMMENDATION . '#' . $id ?>" role="button">戻る</a>
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