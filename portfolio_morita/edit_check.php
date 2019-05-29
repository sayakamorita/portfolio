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
/*index1.php(ユーザー情報登録画面)から送られてくる$_SESSION['edit']に値がない場合の処理*/
if(!isset($_SESSION['edit'])){
    header('Location:edit.php');
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
            <h1><a class="header_title" href="top_p.php">初心者エンジニアのための質問掲示板</a></h1>
        </div>
        <div id="head-right">
            <ul>
            <li><a class="header_link" href="top_p.php">トップページ</a></li>
            <li><a class="header_link" href="list.php">管理者画面</a></li>
            <li><a class="header_link" href="admin_logout.php">ログアウト</a></li>
            </ul>
        </div>
    </div>
<!--ヘッダー終了-->
<!--入力フォーム開始-->
    <div id="content">
        <h2>ユーザー情報編集確認画面</h2>
        <p>入力内容を確認してください。</p>
        <form action="list_edit_complete.php?list_id=<?php print(htmlspecialchars($_SESSION['edit']['id']));?>" method="post">    
            <!--この確認画面で「登録」ボタンをクリックしたか判断するためにinputタグをhidden属性として使う-->
            <input type="hidden" name="action" value="submit">
            <table class="check_form">
                <tr>
                    <td>ニックネーム</td>
                    <!--index1.phpで入力され、セッションに保存された値を出力する-->
                    <td><?php print(htmlspecialchars($_SESSION['edit']['name'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>性別</td>
                    <td><?php print(htmlspecialchars($_SESSION['edit']['sex'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>年齢</td>
                    <td><?php print(htmlspecialchars($_SESSION['edit']['age'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>メールアドレス</td>
                    <td><?php print(htmlspecialchars($_SESSION['edit']['email'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>学習中のプログラミング言語</td>                    
                    <td><?php print(htmlspecialchars($_SESSION['edit']['p_language'],ENT_QUOTES));?></td>
                </tr>
                <tr>
                    <td>パスワード</td>
                    <td><?php print(htmlspecialchars($_SESSION['edit']['password'],ENT_QUOTES));?></td>
                </tr>
            </table>
            <p>この内容でよろしいですか？</p>
            <div class="button_center">
                <ul>
                    <li><input class="button_link2" type="submit" value="登録" ></li>
                    <li><input class="button_link2" type="button" onclick="location.href='edit.php?action=rewrite_edit'" value="戻る"></li>
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
