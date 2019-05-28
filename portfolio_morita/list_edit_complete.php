<?php
session_start();
date_default_timezone_set('Asia/Tokyo');

/*DB接続*/
try{
  $dbs = "mysql:host=127.0.0.1;dbname=portfolio;charset=utf8";
$db_user = "root";
$db_pass = "root";
$db = new PDO($dbs, $db_user, $db_pass);
}catch(PDOException $e){
  print('DB接続エラー：'.$e ->getMessage());
}

    $edit_list = $db ->prepare('UPDATE members SET name=?, sex = ?, age = ?, email=?, p_language = ?, password = ?,modified=NOW() WHERE id = ?');
    /*sql文の?の部分にセッション配列に登録されたそれぞれの値を代入する*/
    $edit_list ->execute(array
        ($_SESSION['edit']['name'],
         $_SESSION['edit']['sex'],
         $_SESSION['edit']['age'],
         $_SESSION['edit']['email'],
         $_SESSION['edit']['p_language'],
         sha1($_SESSION['edit']['password']),
         $_REQUEST['list_id']));
    unset($_SESSION['edit']);
    header('Location:list.php');
    exit();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    　<!--<meta http-equiv="refresh" content="3; URL=top_p.php"> -->
	<title>ユーザー情報編集完了画面</title>
	<link rel="stylesheet" href="style.css" />
</head>
<body>
<div id="wrap">
    <!--ヘッダー開始-->
    <div class="head">
        <div id="head-left">
            <h1>MyPortfolio</h1>
        </div>
    </div>
    <!--ヘッダー終了-->
    <!--コンテンツ開始-->
    <div id="top_content">
        <h2>ユーザー情報の編集が完了しました。</h2>
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
