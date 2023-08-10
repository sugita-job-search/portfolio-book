<?php
require_once('../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//セッションの不要なデータ削除
Session::deleteFormData();

//データベースからユーザー情報とジャンル名取得
try {
    $db = new Users();

    $user = $db->getUserGenreByUserId($_SESSION[Session::USER][Users::ID]);
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
    <title>会員情報確認</title>
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

            <div class="row mb-5">
                <div class="col-lg-9">
                    <div class="row justify-content-center">
                        <div class="col-sm-6">
                            <h2>会員情報</h2>
                            <div class="my-4">
                                <dl>
                                    <dt>アカウント名</dt>
                                    <dd><?= $user[Users::NAME] ?></dd>
                                    <dt>ニックネーム</dt>
                                    <dd><?= $user[Users::NICKNAME] ?></dd>
                                    <dt>好きなジャンル</dt>
                                    <dd><?= $user[Genres::GENRE] == null ? '未選択' : $user[Genres::GENRE] ?></dd>
                                </dl>
                            </div>
                            <div class="d-grid gap-2 d-md-block mt-3">
                                <a href="<?= Common::URL_MEMBER_EDIT ?>" class="btn btn-primary mx-2">会員情報変更</a>
                                <a class="btn btn-outline-secondary mx-2" href="<?= Common::URL_TOP ?>" role="button">トップページに戻る</a>
                            </div>
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
            </div>
        </main>
        <footer>
            <p>&copy;Copyright sugita. All rights reserved</p>
        </footer>
    </div>
</body>

</html>