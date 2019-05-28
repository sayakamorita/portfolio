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

$datas = $db->prepare('SELECT * FROM thread WHERE id = ?');
/*URLパラメータのIDに指定されている数字を取り出す
list.phpの編集ボタンのURLパラメーターpage_listでおくられてきた数字を、上のSQL文のid＝？に代入して値を取り出す*/
$datas ->execute(array($_REQUEST['thread_id']));
$data = $datas->fetch();

/*削除ボタンを押下した時の削除処理*/
    $statement = $db->prepare('DELETE FROM thread WHERE id = ?');
    $statement ->execute(array($data['id']));
    header('Location:thread_'.$data["thread_id"].'.php');
    exit();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ユーザー情報削除確認画面</title>
    <link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
<!--ヘッダー開始-->
    <div class="head">
        <div id="head-left">
        <h1><a href="../top_p.php">MyPortfolio</a></h1>
        </div>

        <div id="head-right">
            <ul>
                <li><a href="top_p.php">トップページ</a></li>
                <li>Github</li>
            </ul>
        </div>
    </div>
<!--ヘッダー終了-->
<!--入力フォーム開始-->
    <div id="content">
        <h2>ユーザー情報削除確認画面</h2>
        <p>以下のスレッドを削除してもよろしいですか？</p>
        <form action="list.php" method="post">    
            <!--この確認画面で「登録」ボタンをクリックしたか判断するためにinputタグをhidden属性として使う-->
            <input type="hidden" name="action" value="submit">
            <table>
                <tr>
                    <td>タイトル</td>
                    <td><?php print(htmlspecialchars($data['title'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>質問内容</td>
                    <!--list.phpの削除ボタンのURLパラメーターpage_listでおくられてきたされた値を出力する-->
                    <td><?php print(htmlspecialchars($data['message'],ENT_QUOTES));?></td>
                </tr>
            <div class="btn"><a href="../thread_<?php print(htmlspecialchars($data['thread_id']));?>.php">戻る</div>
            <a href="delete_thread_complete.php?delete_complete=<?php print(htmlspecialchars($data['thread_id']));?>">削除
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
