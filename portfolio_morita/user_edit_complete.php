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

    $statement = $db ->prepare('UPDATE members SET name=?, sex = ?, age = ?, email=?, p_language = ?, password = ?,modified=NOW() WHERE id = ?');
    /*sql文の?の部分にセッション配列に登録されたそれぞれの値を代入する*/
    $statement ->execute(array
        ($_SESSION['user_edit']['name'],
         $_SESSION['user_edit']['sex'],
         $_SESSION['user_edit']['age'],
         $_SESSION['user_edit']['email'],
         $_SESSION['user_edit']['p_language'],
         sha1($_SESSION['user_edit']['password']),
         $_REQUEST['user_id']));
    unset($_SESSION['user_edit']);
   header('Location:top_p.php');
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
