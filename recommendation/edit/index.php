<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//エラーメッセージと入力履歴があれば取得
$display = new Display(Common::URL_RECOMMENDATION_EDIT);
Session::deleteFormData();

//1以上の自然数が送られてこなかった場合はリダイレクト
$id = Input::filterIdGet(Input::RECOMMENDATION_ID);

if ($id == '') {
    Common::redirect(Common::URL_RECOMMENDATION);
}

//ワンタイムトークン生成
$token = Session::generateToken();

//推薦文と本の情報取得
try {
    $db = new Recommendations();

    $recommendation = $db->getRecommendationBookByRecommendationId($id);

    //取得できなければリダイレクト
    if (!$recommendation) {
        Common::redirect(Common::URL_RECOMMENDATION);
    }

    //著者を一人ずつに分割
    $authors = explode("\n", $recommendation[Books::AUTHOR]);

    //書影がない場合はno imageの画像を表示
    $image = Common::returnImageName($recommendation[Books::IMAGE]);

    //入力履歴があるときはそれを表示、ないときはデータベースのデータ表示
    $text = $display->getHistory(Recommendations::RECOMMENDATION);

    if ($text == '') {
        $text = $recommendation[Recommendations::RECOMMENDATION];
    }

    //セッションunconfirmedに推薦文のid保存
    Session::saveUnconfirmedData(Common::URL_RECOMMENDATION_EDIT, Recommendations::ID, $id);
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
    <title>推薦文編集</title>
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
                    <div class="row">
                        <div class="col-sm-3 mb-2">
                            <img src="../../img/<?= $image ?>" alt="" class="img-fluid">
                        </div>
                        <div class="col-sm-8 mx-2">
                            <h3><?= $recommendation[Books::TITLE] ?></h3>
                            <table class="table table-borderless">
                                <tr>
                                    <th>著者</th>
                                    <td>
                                        <div class="row row-cols-auto">
                                            <?php foreach ($authors as $a) : ?>
                                                <div class="col">
                                                    <?= $a ?>
                                                </div>
                                            <?php endforeach ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>出版社</th>
                                    <td><?= $recommendation[Books::PUBLISHER] ?></td>
                                </tr>
                                <tr>
                                    <th>出版年月</th>
                                    <td><?= $recommendation[Books::YEAR] ?>年<?= $recommendation[Books::MONTH] ?>月</td>
                                </tr>
                                <tr>
                                    <th>ISBN</th>
                                    <td><?= $recommendation[Books::ISBN] ?></td>
                                </tr>
                                <?php if ($recommendation[Books::SERIES_TITLE] !== '') : ?>
                                    <tr>
                                        <th>シリーズ名</th>
                                        <td><?= $recommendation[Books::SERIES_TITLE] ?></td>
                                    </tr>
                                <?php endif ?>
                                <tr>
                                    <th>ジャンル</th>
                                    <td><?= $recommendation[Genres::GENRE] ?></td>
                                </tr>
                            </table>
                        </div>

                        <h2>推薦文編集</h2>
                        <form action="./process.php" method="post" id="form">
                            <p>推薦文を編集してください（500文字以内）</p>
                            <div class="mb-3">
                                <textarea name="recommendation" class="form-control" rows="5" aria-label="推薦文"><?= $text ?></textarea>
                                <?= $display->getMessage(Input::RECOMMENDATION) ?>
                                <input type="hidden" name="<?= Input::TOKEN ?>" value="<?= $token ?>">
                                <!-- <div class="error-message">推薦文を入力してください</div> -->
                            </div>
                            <div class="d-grid gap-2 d-md-block">
                                <button type="submit" class="btn btn-primary mx-2">完了</button>
                                <a class="btn btn-outline-secondary mx-2" href="<?= Common::URL_RECOMMENDATION . '#' . $id ?>" role="button">あなたの推薦文一覧に戻る</a>
                            </div>
                        </form>
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