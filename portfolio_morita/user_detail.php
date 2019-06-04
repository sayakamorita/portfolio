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

//ログインしたままの状態を更新する
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
    $_SESSION['time'] = time();
    $members = $db ->prepare('SELECT * FROM admin WHERE id = ?');
    $members ->execute(array($_SESSION['id']));
    $member = $members ->fetch();
}else{
    header('Location:top_p.php');
    exit();
}

/*ログイン画面からトップ画面に遷移した際のhiddenタグを消去する*/
if(isset($_SESSION['login'])){
    unset($_SESSION['login']);
}
/*スレッドをたてた時に持っているhiddenでもっているflgを消去する*/
if(isset($_SESSION['create_thread']['flg'])){
    unset($_SESSION['create_thread']['flg']);
}

/*DBから投稿を取得する*/
$members = $db ->prepare('SELECT * FROM members WHERE id = ?');
$members->execute(array($_REQUEST['id']));
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>登録者情報一覧画面</title>
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
<div id="wrap">
     <!--ヘッダー開始-->
     <nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#000000;">
        <a class="navbar-brand" href="top_p.php">Threads For Fledgeling Engineers</a>   
    <button class="navbar-toggler" data-toggle="collapse" data-target=#navbarNav>
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse navbar-right" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active"><a class="nav-link" href="top_p.php">トップページ</a></li>
            <li class="nav-item"><a class="nav-link" href="logout_p.php">ログアウト</a></li>
        </ul>
        </div>
    </nav>
<!--ヘッダー終了-->
    <!--コンテンツ開始-->
    <div id="top_content">
        <h2>登録者情報</h2>
        <!--tableで全ての情報一覧を取得する-->
        <table style="margin : 0 auto">
            <tr>
                <td>ニックネーム</td>
                <td>メールアドレス</td>
                <td>学習中のプログラミング言語</td>
                <td>性別</td>
                <td>年齢</td>
            </tr>
            <tr></tr>
        <?php 
        foreach($members as $member){
            echo '<tr>';
            echo '<td>'.htmlspecialchars($member['name']).' '.'</td>';
            echo '<td>'.htmlspecialchars($member['email']).' '.'</td>';
            echo '<td>'.htmlspecialchars($member['p_language']).' '.'</td>';
            echo '<td>'.htmlspecialchars($member['sex']).' '.'</td>';
            echo '<td>'.htmlspecialchars($member['age']).'   '.'</td>';
            /*編集ボタンも、URLパラメーターを使ってforeach文の中にいれる。
            ゲストユーザーは内容を編集できないようにしておく*/
            if($member['id'] !== '22'){
            /* URLパラメータを使って、該当IDのedit.phpに遷移する*/
            echo '<td><a class="button_link" href="user_edit.php?id='.htmlspecialchars($member['id']).'">編集</a>'.'  '.'</td>';
            /*詳細ボタンも、URLパラメーターを使ってこの中にいれこむ*/
            echo '</tr>';
            echo '<br>';
            }
        }?>
        </table>
        <br>
        <br>
        <div id="button-center">
            <ul>
            <li><a class="button_link" href="top_p.php">トップページに戻る</a></li>
            <li><a class="button_link" href="logout_p.php">ログアウト</a></li>
            </ul>
        </div>

    </div>
    <!--コンテンツ終了-->
    <!--フッター終了-->
    <div class="footer">
        <p>©︎2019 morita</p>
    </div>
    <!--フッター終了-->
</div>
</body>
</html>