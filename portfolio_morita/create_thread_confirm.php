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

/*ユーザーのログイン情報があった場合の処理*/
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
    $_SESSION['time'] = time();
    $members = $db ->prepare('SELECT * FROM members WHERE id = ?');
    $members ->execute(array($_SESSION['id']));
    $member = $members ->fetch();
}else{
/*ログインしていなかったら、ログイン画面に遷移する*/
    header('Location:login_p.php');
    exit();
}

/*このまま投稿されなかったら、create_thread.phpで持たせたhiddenフラグを折る*/
/*if(empty($_POST) && isset($_SESSION['create_thread']['flg'])){
    unset($_SESSION['create_thread']['flg']);
}*/

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>確認画面</title>
    <link rel="stylesheet" href="../style.css" />
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
        <a class="navbar-brand" href="top_p.php"></a>   
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
<!--入力フォーム開始-->
    <div id="content">
        <h2>スレッド内容確認画面</h2>
        <p>入力内容を確認してください。</p>
        <form action="create_thread_complete.php" method="post">    
            
            <div class="check_form">
                
                <h3>言語：<?php print(htmlspecialchars($_SESSION['create_thread']['language_category'],ENT_QUOTES));?></h3>
                <hr>
                    <h3>タイトル</h3>
                    <!--index1.phpで入力され、セッションに保存された値を出力する-->
                    <p><?php print(htmlspecialchars($_SESSION['create_thread']['question_title'],ENT_QUOTES));?></p>
                    <hr>
                    <h3>名前：<?php print(htmlspecialchars($member['name'],ENT_QUOTES));?></h3>
                    <hr>
                <div class="thread_content_check">
                    <h3>質問内容</h3>
                        <p><?php print(htmlspecialchars($_SESSION['create_thread']['p_question'],ENT_QUOTES));?></p>
                </div>
            </div>
            <br>
            <p>この内容でよろしいですか？</p>
            <br>
            <div class="button_center">
                <ul>
                    <li><input class="button_link" type="submit" value="投稿" ></li>
                    <li><input class="button_link" type="button" onclick="location.href='create_thread.php?action_thread=rewrite_thread'" value="修正する"></li>
                </ul>
            </div>
        </form>
    </div>
<!--入力フォーム終了-->
<!--フッター開始-->
    <div class="footer">
        <p>©︎2019 morita</p>    
    </div>
<!--フッター終了-->
</div>
</body>
</html>
