<?php
session_start();
/*DB接続*/
try{
$dbs = "mysql:host=127.0.0.1;dbname=portfolio;charset=utf8";
$db_user = "root";
$db_pass = "root";
$db = new PDO($dbs, $db_user, $db_pass);
}catch(PDOException $e){
print('DB接続エラー：'.$e ->getMessage());
}

if($_REQUEST['flg'] ==='on'){
unset($_SESSION['create_thread']['flg']);
}

//スレッドを立てる画面で、何かの言語を選択し、投稿しないままトップ画面に戻ったらそのセッションの値を消す
if($_REQUEST['flg'] ==='on'){
unset($_SESSION['create_thread']['language_category']);
}

//ログインしたままの状態を更新する
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
$_SESSION['time'] = time();
$members = $db ->prepare('SELECT * FROM members WHERE id = ?');
$members ->execute(array($_SESSION['id']));
$member = $members ->fetch();
/*5通りのランダムな数字を用意しておく（セリフをかえるため)*/
$rand= rand(0,2);
/*}else{
header('Location:login_p.php');
exit();*/
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>トップ画面</title>
<link rel="stylesheet" href="style.css" />
<!--bootstrap読み込み-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script type="text/javascript" src="portfolio.js"></script>
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
</head>
<body>
<div id="wrap" >

<!--ヘッダー開始-->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#000000;">
        <a class="navbar-brand" href="top_p.php"></a>   
    <button class="navbar-toggler" data-toggle="collapse" data-target=#navbarNav>
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse navbar-right" id="navbarNav">
        <ul class="navbar-nav">
        <!--ログインしていたらログアウトボタンを、ログアウト状態であればログインボタンをヘッダーに表示させる-->
        <?php if(!isset($_SESSION['id'])):?>
        <li class="nav-item active"><a class="nav-link" href="login_p.php">ログイン</a></li>
        <?php endif;?>

        <?php if(isset($_SESSION['id'])):?>
        <li class="nav-item"><a class="nav-link" href="user_detail.php?id=<?php print(htmlspecialchars($_SESSION['id']));?>">ユーザー登録情報</a></li>
        <?php endif;?>

        <?php if(!isset($_SESSION['id'])):?>
        <li class="nav-item"><a class="nav-link" href="admin.php">管理者ログイン</a></li>
        <?php endif;?>

        <?php if(!isset($_SESSION['id'])):?>
        <li class="nav-item"><a class="nav-link" href="index1_p.php">ユーザー登録</a></li>
        <?php endif;?>

        <?php if(isset($_SESSION['id'])):?>
        <li class="nav-item"><a class="nav-link" href="create_thread.php">スレッドを立てる</a></li>
        <?php endif;?>

        <?php if(isset($_SESSION['id'])):?>
        <li class="nav-item"><a class="nav-link" href="logout_p.php">ログアウト</a></li>
        <?php endif;?>
        </ul>
        </div>
    </nav>
<!--ヘッダー終了-->
<!--コンテンツ開始-->
<div id="content">
    <div class="comment">
        <div class="balloon5">
            <div class="faceicon">
                <img src="https://www.soko-soko.com/wp-content/uploads/2019/01/icon-morifukase-2c1.jpeg" width="120px" height="120px">
            </div>
        <div class="chatting">
            <div class="says">
                <?php if(!isset($_SESSION['id'])):?>
                    <p id="a">初心者エンジニアのみなさん、こんにちは！</p>
                    <p id="b">まずはログイン、もしくはユーザー登録をお願いします。</p>
                    <p id="c">ゲストユーザーでログイン→【ID：guest、パスワード：guest】になります！</p>
                <?php endif;?>

                <!--ログイン画面から遷移してきた時のメッセージ(ログイン画面のhidden属性を使う)-->
                <?php if($_SESSION['login'] === '1'):?>
                    <p id="a"><?php print(htmlspecialchars($_SESSION['name']))?>さんのログインが無事に完了しました！</p>
                    <p id="b">下から質問したい言語を選んで、学習のために活用してみてくださいね。</p>
                    <p id="c">一緒にプログラミング学習頑張りましょう！</p>
                <?php endif;?>

                <!--スレッドを立てたあとに表示されるメッセージ(create_thread_confirmのhidden属性を使う)-->
                <?php if($_SESSION['create_thread']['flg'] === '1'):?>
                    <p id="a">スレッドの投稿が完了しました！</p>
                    <p id="b">自分の質問に回答してくれた人がいたら、必ず返信するようにしましょう。</p>
                    <p id="c">誰もが気持ちのいいやりとりのできるサイトにしていきましょうね！</p>
                <?php endif;?>

                <!--上のランダムで出た数字を使い、表示する文字列をかえる。-->
                <!--$rand === '1'ではうごかず、 == にする必要がある-->
                <?php if(empty($_SESSION['login']) && empty($_SESSION['create_thread']['flg']) && $rand == '0'):?>
                    <p id="a">こんなことを質問していいのかなと思ったら、</p>
                    <p id="b">まずは一度過去の質問一覧に目を通してみてください。</p>
                    <p id="c">30分調べてみてわからなかったことは、積極的に質問してみましょう！</p>
                <?php endif;?>

                <?php if(empty($_SESSION['login']) && empty($_SESSION['create_thread']['flg']) && $rand == '1'):?>
                    <p id="a">プログラミング学習に疲れたら、少し休憩しませんか？</p>
                    <p id="b">飲み物を飲んだり甘いものを食べたりすると、</p>
                    <p id="c">頭もスッキリして解決しなかったエラーが一瞬で解決するかも？！</p>
                <?php endif;?>

                <?php if(empty($_SESSION['login']) && empty($_SESSION['create_thread']['flg']) && $rand == '2'):?>
                    <p id="a">オススメの情報収集ツールの一つはTwitterです。</p>
                    <p id="b">みなさんと同じく駆け出しエンジニアの方がたくさんいるので、</p>
                    <p id="c">彼らと交流しながら役立つ情報を得ることができますよ！</p>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>

<!--login_p.phpで$COOKIE['email']に値が入っていたら、そのまま掲示板の画面にとべるように、URLを指定する-->
<!--<div class="top">-->
<div class="container">
<!--<div class="top_inline_block">-->
<div class="row">
<div class="top_inline_block col-xs-3 col-lg-3">
<a href="../thread_java.php">
<img src="https://www.soko-soko.com/wp-content/uploads/2019/05/java.png"></a>
</div>

<div class="top_inline_block col-xs-3 col-lg-3">
<a class="top4" href="../thread_js.php">
<img src="https://www.soko-soko.com/wp-content/uploads/2019/05/javascript.png"></a>
</div>
<div class="top_inline_block col-xs-3 col-lg-3">
<a class="top4" href="../thread_php.php">
<img src="https://www.soko-soko.com/wp-content/uploads/2019/05/php.png"></a>
</div>
<div class="top_inline_block col-xs-3 col-lg-3">
<a class="top4" href="../thread_ruby.php">
<img src="https://www.soko-soko.com/wp-content/uploads/2019/05/ruby.png"></a>
</div>
</div>
</div>

<div class="container">
<div class="row">
<div class="top_inline_block col-xs-4 col-lg-3">
<a class="bottom4" href="../thread_python.php">
<img src="https://www.soko-soko.com/wp-content/uploads/2019/05/python.png"></a>
</div>
<div class="top_inline_block col-xs-4 col-lg-3">
<a class="bottom4" href="../thread_html_css.php">
<img src="https://www.soko-soko.com/wp-content/uploads/2019/05/html_css.png"></a>
</div>
<div class="top_inline_block col-xs-4 col-lg-3">
<a class="bottom4" href="../thread_laravel.php">
<img src="https://www.soko-soko.com/wp-content/uploads/2019/05/laravel.png"></a>
</div>
<div class="top_inline_block col-xs-4 col-lg-3">
<a class="bottom4" href="../thread_rails.php">
<img src="https://www.soko-soko.com/wp-content/uploads/2019/05/rails.png"></a>
</div>
</div>
</div>
</div>
<!--コンテンツ終了-->
<!--フッター開始-->
<div class="footer">
<p>©︎2019 morita</p>
</div>
<!--フッター終了-->
</div>
</body>
</html>
