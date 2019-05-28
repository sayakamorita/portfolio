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
/*index1.php(ユーザー情報登録画面)から送られてくる$_SESSION['join']に値がない場合の処理*/
if(!isset($_SESSION['join'])){
    header('Location:index1_p.php');
    exit();
}
/*inputのhidden属性を指定したので、この確認画面でフォームの値が送信された状態であればDBに接続するという処理*/
if(!empty($_POST)){
    $statement = $db ->prepare('INSERT INTO members SET name=?, sex = ?, age = ?, email=?, p_language = ?, password = ?,created=NOW()');
    /*sql文の?の部分にセッション配列に登録されたそれぞれの値を代入する*/
    $statement ->execute(array
        ($_SESSION['join']['name'],
         $_SESSION['join']['sex'],
         $_SESSION['join']['age'],
         $_SESSION['join']['email'],
         $_SESSION['join']['p_language'],
         sha1($_SESSION['join']['password'])
        ));
    unset($_SESSION['join']);
    header('Location:thanks_p.php');
    exit();
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>確認画面</title>
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
        <h2>ユーザー情報確認画面</h2>
        <p>入力内容を確認してください。</p>
        <form action="" method="post">    
            <!--この確認画面で「登録」ボタンをクリックしたか判断するためにinputタグをhidden属性として使う-->
            <input type="hidden" name="action" value="submit">
            <table>
                <tr>
                    <td>ニックネーム</td>
                    <!--index1.phpで入力され、セッションに保存された値を出力する-->
                    <td><?php print(htmlspecialchars($_SESSION['join']['name'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>性別</td>
                    <td><?php print(htmlspecialchars($_SESSION['join']['sex'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>年齢</td>
                    <td><?php print(htmlspecialchars($_SESSION['join']['age'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>メールアドレス</td>
                    <td><?php print(htmlspecialchars($_SESSION['join']['email'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>学習中のプログラミング言語</td>                    
                    <td><?php print(htmlspecialchars($_SESSION['join']['p_language'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>パスワード</td>
                    <td><?php print(htmlspecialchars($_SESSION['join']['password'],ENT_QUOTES));?></td>
                </tr>
            </table>
            <p>この内容でよろしいですか？</p>
            <div class="btn"><a href="index1_p.php?action=rewrite">戻る</div>
            <input type="submit" value="登録">
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
