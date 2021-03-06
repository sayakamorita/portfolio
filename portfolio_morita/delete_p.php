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

if(isset($_SESSION['id'])){
    $id = $_REQUEST['id'];
    $messages = $db->prepare('SELECT * FROM post WHERE id = ?');
    $messages ->execute(array($id));
    $message = $messages->fetch();

    if($message['member_id'] === $_SESSION['id']){
        $del = $db ->prepare('DELETE FROM post WHERE id = ?');
        $del ->execute(array($id));

    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    　<meta http-equiv="refresh" content="3; URL=../index2_p.php">
	<title>トップ画面</title>
	<link rel="stylesheet" href="style.css" />
</head>
<body>
<div id="wrap">
    <!--ヘッダー開始-->
    <div class="head">
        <div id="head-left">
            <h1>MyPortfolio</h1>
        </div>

        <div id="head-right">
            <ul>
                <li>ログイン</li>
                <li>Github</li>
            </ul>
        </div>
    <!--ヘッダー終了-->
    <!--コンテンツ開始-->
    <div id="content">
        <h2>メッセージを削除しました。</h2>
        <p>3秒後につぶやき画面に遷移します</p>
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