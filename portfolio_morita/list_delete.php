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
/*list.php(ユーザー情報一覧画面)から送られてくる$_SESSION['edit']に値がない場合の処理*/
/*if(!isset($_SESSION['edit'])){
    header('Location:list.php');
    exit();
}*/

$datas = $db->prepare('SELECT * FROM members WHERE id = ?');
/*URLパラメータのIDに指定されている数字を取り出す
list.phpの削除ボタンのURLパラメーターdelete_listでおくられてきた数字を、上のSQL文のid＝？に代入して値を取り出す*/
$datas ->execute(array($_REQUEST['delete_list']));
$data = $datas->fetch();

/*inputのhidden属性を指定したので、この確認画面でフォームの値が送信された状態であればDBに接続するという処理*/
    $statement = $db->prepare('DELETE FROM members WHERE id = ?');
    /*sql文の?の部分にセッション配列に登録されたそれぞれの値を代入する*/
    $statement ->execute(array($_REQUEST['delete_list']));
    /*削除後、ユーザー情報一覧画面に遷移する*/
    header('Location:list.php');
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
        <p>以下のデータを削除してもよろしいですか？</p>
        <form action="list.php" method="post">    
            <!--この確認画面で「登録」ボタンをクリックしたか判断するためにinputタグをhidden属性として使う-->
            <input type="hidden" name="action" value="submit">
            <table>
                <tr>
                    <td>ID</td>
                    <td><?php print(htmlspecialchars($data['id'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>ニックネーム</td>
                    <!--list.phpの削除ボタンのURLパラメーターpage_listでおくられてきたされた値を出力する-->
                    <td><?php print(htmlspecialchars($data['name'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>性別</td>
                    <td><?php print(htmlspecialchars($data['sex'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>年齢</td>
                    <td><?php print(htmlspecialchars($data['age'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>メールアドレス</td>
                    <td><?php print(htmlspecialchars($data['email'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>学習中のプログラミング言語</td>                    
                    <td><?php print(htmlspecialchars($data['p_language'],ENT_QUOTES));?></td>
                </tr>
                
                <tr>
                    <td>パスワード</td>
                    <td><?php print(htmlspecialchars($data['password'],ENT_QUOTES));?></td>
                </tr>
            </table>
            <div class="btn"><a href="list.php">戻る</div>
            <input type="submit" value="削除">
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
