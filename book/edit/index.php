<?php
require_once('../../app/config.php');

//ログイン状態にないときログイン画面にリダイレクト
Session::confirmLogin();

//1以上の自然数が送られてこなかった場合はリダイレクト
$id = Input::filterIdGet(Input::BOOK_ID);

if ($id == '') {
    Common::topRedirect();
}

//エラーメッセージと入力履歴が存在するときはセッションから取得
$display = new Display(Common::URL_BOOK_EDIT);
Session::deleteFormData();

try {
    //データベースから本の情報を取得、情報がなかった場合はリダイレクト
    $db = new Books();
    $book = $db->getBookById($id);

    if (!$book) {
        Common::topRedirect();
    }

    //セッションunconfirmedにid保存
    Session::saveUnconfirmedData(Common::URL_BOOK_EDIT, Books::ID, $id);

    //画像の名前を取得
    $image = $book[Books::IMAGE];

    //入力履歴があるときはそれを表示
    $histories = $display->getHistories();
    if ($histories != []) {
        $book = $histories;
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
    <title>本の情報変更</title>
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
                    <div class="row justify-content-center">
                        <div class="col-sm-9">
                            <h2>本の情報の変更</h2>
                            <p>新しい情報を入力してください</p>
                            <form action="./process.php" method="post" enctype="multipart/form-data" class="my-3">
                                <div class="mb-3">
                                    <label for="title" class="form-label">タイトル（必須）</label>
                                    <input type="text" class="form-control" name="title" id="title" value="<?= $book[Books::TITLE] ?>" />
                                    <?= $display->getMessage(Input::TITLE) ?>
                                    <!-- <div class="error-message">
                                        タイトルを入力してください
                                    </div> -->
                                </div>
                                <div class="mb-3">
                                    <label for="author" class="form-label">著者（必須）</label>
                                    <div class="form-text">複数の著者がいる場合は改行して入力してください</div>
                                    <textarea class="form-control" name="author" id="exampleFormControlTextarea1" rows="3"><?= $book[Books::AUTHOR] ?></textarea>
                                    <?= $display->getMessage(Input::AUTHOR) ?>
                                    <!-- <div class="error-message">
                                        著者を入力してください
                                    </div> -->
                                </div>
                                <div class="mb-3">
                                    <label for="publisher" class="form-label">出版社（必須）</label>
                                    <input type="text" class="form-control" name="publisher" id="publisher" value="<?= $book[Books::PUBLISHER] ?>" />
                                    <?= $display->getMessage(Input::PUBLISHER) ?>
                                    <!-- <div class="error-message">
                                        出版社を入力してください
                                    </div> -->
                                </div>
                                <div class="mb-3">
                                    <div class="form-label">出版年月（必須）</div>
                                    <div class="form-text">西暦で入力してください</div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="year" aria-label="出版年" value="<?= $book[Books::YEAR] ?>">
                                        <span class="input-group-text">年</span>
                                        <select class="form-select" name="month" aria-label="出版月">
                                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                                <option value="<?= $i ?>" <?php if ($book[Books::MONTH] == $i) echo 'selected' ?>><?= $i ?></option>
                                            <?php endfor ?>
                                            <!-- <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option> -->
                                        </select>
                                        <span class="input-group-text">月</span>
                                    </div>
                                    <?= $display->getMessage(Input::YEAR_AND_MONTH) ?>
                                    <!-- <div class="error-message">
                                        出版年月を入力してください
                                    </div> -->
                                </div>
                                <div class="mb-3">
                                    <label for="series" class="form-label">シリーズ名</label>
                                    <input type="text" name="series_title" class="form-control" id="series" value="<?= $book[Books::SERIES_TITLE] ?>" />
                                    <?= $display->getMessage(Input::SERIES) ?>
                                    <!-- <div class="error-message">
                                        シリーズ名は100文字以内で入力してください
                                    </div> -->
                                </div>
                                <div class="mb-3">
                                    <label for="genre" class="form-label">ジャンル（必須）</label>
                                    <select class="form-select" name="genre_id" id="genre">
                                        <option value="0" selected>選択されていません</option>
                                        <?php foreach ($genres as $key => $genre) : ?>
                                            <option value="<?= $key ?>" <?php if ($book[Books::GENRE_ID] == $key) echo 'selected' ?>><?= $genre ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <?= $display->getMessage(Input::GENRE) ?>
                                    <!-- <div class="error-message">
                                        不正な値が選択されました
                                    </div> -->
                                </div>
                                <div class="mb-3">
                                    <label for="formFile" class="form-label">表紙の画像</label>
                                    <?php if ($image != '') : ?>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <div class="form-text">現在の画像</div>
                                                <img src="../../img/<?= $image ?>" alt="" class="img-fluid">
                                            </div>
                                        </div>
                                    <?php endif ?>
                                    <div class="form-text">PNGまたはJPEGファイルがアップロードできます</div>
                                    <input class="form-control" type="file" name="image" id="formFile">
                                    <?= $display->getMessage(Input::IMAGE) ?>
                                    <!-- <div class="error-message">
                                        その形式のファイルはアップロードできません
                                    </div> -->
                                </div>

                                <input type="hidden" name="<?= Input::TOKEN ?>" value="<?= Session::generateToken() ?>">
                                <div class="d-grid gap-2 d-md-block mt-3">
                                    <button type="submit" class="btn btn-primary mx-2">完了</button>
                                    <a class="btn btn-secondary mx-2" href="../?<?= Input::BOOK_ID ?>=<?= $id ?>" role="button">戻る</a>
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