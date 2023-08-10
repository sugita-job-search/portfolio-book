<?php
require_once('../../app/config.php');

//セッションに保存されているエラーメッセージと入力内容を取得
$display = new Display(Common::URL_LOGIN);
Session::deleteFormData();
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ログイン</title>
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
            <form action="./process.php" method="post">
                <?= $display->getMessage('login') ?>
                <input type="hidden" name="<?= Input::TOKEN ?>" value="<?= Session::generateToken() ?>">
                <div class="my-3">
                    <label for="name" class="form-label">アカウント名</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?= $display->getHistory(Users::NAME) ?>" />
                </div>
                <div class="mb-5">
                    <label for="Password" class="form-label">パスワード</label>
                    <input type="password" class="form-control" name="password" id="Password" />
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary mb-5">
                        ログイン
                    </button>

                </div>
            </form>
            <div>
                登録がお済みでない方はこちら：<a href="<?= Common::URL_MEMBER_REGISTRATION ?>">会員登録</a>
            </div>
        </main>
        <footer>
            <p>&copy;Copyright sugita. All rights reserved</p>
        </footer>
    </div>
</body>

</html>