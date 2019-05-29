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
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    　<meta http-equiv="refresh" content="3; URL=top_p.php">
	<title>トップ画面</title>
	<link rel="stylesheet" href="style.css" />
</head>
<body>
<div id="wrap">
    <!--ヘッダー開始-->
    <div class="head">
        <div id="head-left">
            <h1>初心者エンジニアのための質問掲示板</h1>
        </div>
    <!--ヘッダー終了-->
    <!--コンテンツ開始-->
    <div id="top_content">
        <h2>ユーザー登録が完了しました。</h2>
        <p>3秒後にトップページに遷移します</p>
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