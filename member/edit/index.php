<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//エラーメッセージと入力履歴が存在するときはセッションから取得
$display = new Display(Common::URL_MEMBER_EDIT);
Session::deleteFormData();

$values = $display->getHistories();

//ワンタイムトークン生成
$token = Session::generateToken();

try {
    //入力履歴がないときデータベースからユーザー情報取得して表示
    if ($values == []) {
        $db = new Users();

        $values = $db->getUserById($_SESSION[Session::USER][Users::ID]);
    }

    //全ジャンルを取得
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
    <title>会員情報変更</title>
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
            <form action="<?= Common::URL_SEARCH ?>" class="d-flex justify-content-end top-search">
                <input type="text" class="form-control form-control-sm top-input" id="search" placeholder="書名や著者名で検索" aria-label="検索">
                <button type="submit" class="btn top-btn btn-primary btn-sm">検索</button>
            </form>

            <div class="row">
                <div class="col-lg-9">
                    <div class="row justify-content-center">
                        <div class="col-sm-9">
                            <h2>会員情報変更</h2>
                            <p>新しい会員情報を入力してください</p>
                            <form action="./process.php" method="post" class="my-3">
                                <input type="hidden" name="<?= Input::TOKEN ?>" value="<?= $token ?>">
                                <div class="mb-3">
                                    <label for="name" class="form-label">アカウント名</label>
                                    <input type="text" name="<?= Users::NAME ?>" class="form-control" id="name" value="<?= $values[Users::NAME] ?>" />
                                    <?= $display->getMessage(Input::NAME) ?>
                                </div>
                                <div class="mb-3">
                                    <label for="nickname" class="form-label">ニックネーム</label>
                                    <input type="text" name="<?= Users::NICKNAME ?>" class="form-control" id="nickname" value="<?= $values[Users::NICKNAME] ?>" />
                                    <?= $display->getMessage(Input::NICKNAME) ?>
                                </div>
                                <div class="mb-3">
                                    <label for="Password" class="form-label">パスワード</label>
                                    <input type="password" name="<?= Users::PASSWORD ?>" class="form-control" id="Password" />
                                    <?= $display->getMessage(Input::PASSWORD) ?>
                                </div>
                                <div class="mb-3">
                                    <label for="genre">好きなジャンル（任意）</label>
                                    <select name="<?= Users::GENRE_ID ?>" class="form-select" id="genre">
                                        <option value="0">選択されていません</option>
                                        <?php foreach ($genres as $k => $g) : ?>
                                            <option value="<?= $k ?>" <?php if ($k == $values[Users::GENRE_ID]) echo 'selected' ?>><?= $g ?></option>
                                        <?php endforeach ?>
                                        
                                    </select>
                                    <?= $display->getMessage(Input::FAVORITE_GENRE) ?>
                                </div>
                                <div class="d-grid gap-2 d-md-block mt-3">
                                    <button type="submit" class="btn btn-primary mx-2">変更</button>
                                    <a class="btn btn-outline-secondary mx-2" href="<?= Common::URL_MEMBER ?>" role="button">会員情報確認ページに戻る</a>
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