<?php
require_once('../../app/config.php');

try {
    $db = new Genres();

    $genres = $db->getGenres();
} catch (Exception $e) {
    Common::errorRedirect();
}

$display = new Display(Common::URL_MEMBER_REGISTRATION);
Session::deleteFormData();

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>会員登録</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />
    <link rel="stylesheet" href="../../css/style.css" />
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a href="<?= Common::URL_TOP ?>" class="navbar-brand">LaraBook</a>
            </div>
        </nav>
    </header>
    <div class="narrow-container">
        <main>
            <h2>会員登録</h2>
            <p>
                会員情報を入力してください。<br>
                登録後は会員情報確認ページから会員情報を変更することができます。
            </p>
            <form action="./process.php" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">アカウント名</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?= $display->getHistory(Users::NAME) ?>" />
                    <?= $display->getMessage(Input::NAME) ?>
                </div>
                <div class="mb-3">
                    <label for="nickname" class="form-label">ニックネーム</label>
                    <input type="text" class="form-control" name="nickname" id="nickname" value="<?= $display->getHistory(Users::NICKNAME) ?>" />
                    <?= $display->getMessage(Input::NICKNAME) ?>
                </div>
                <div class="mb-3">
                    <label for="Password" class="form-label">パスワード</label>
                    <input type="password" class="form-control" name="password" id="Password" />
                    <?= $display->getMessage(Input::PASSWORD) ?>
                </div>
                <div class="mb-3">
                    <label for="genre" class="form-label">好きなジャンル（任意）</label>
                    <select class="form-select" name="genre_id" id="genre">
                        <option value="0">選択されていません</option>
                        <?php foreach ($genres as $key => $genre) : ?>
                            <option value="<?= $key ?>" <?php if ($display->getHistory(Users::GENRE_ID) == $key) echo 'selected' ?>><?= $genre ?></option>
                        <?php endforeach ?>
                    </select>
                    <?= $display->getMessage(Input::FAVORITE_GENRE) ?>
                </div>
                <input type="hidden" name="<?= Input::TOKEN ?>" value="<?= Session::generateToken() ?>">
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary mx-2 mb-5">
                        会員登録
                    </button>

                </div>
            </form>
            <p>
                会員の方はこちら：<a href="<?= Common::URL_LOGIN ?>">ログイン</a>
            </p>

        </main>
        <footer>
            <p>&copy;Copyright sugita. All rights reserved</p>
        </footer>
    </div>
</body>

</html>