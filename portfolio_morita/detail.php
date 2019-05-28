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
if(isset($_SESSION['admin_id']) && $_SESSION['time'] + 3600 > time()){
    $_SESSION['time'] = time();
    $members = $db ->prepare('SELECT * FROM admin WHERE id = ?');
    $members ->execute(array($_SESSION['admin_id']));
    $member = $members ->fetch();
}else{
    header('Location:admin.php');
    exit();
}

/*DBから投稿を取得する*/
$members = $db ->prepare('SELECT * FROM members WHERE id = ?');
$members->execute(array($_REQUEST['page_list']));
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>登録者情報一覧画面</title>
	<link rel="stylesheet" href="style.css" />
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
            <li><a class="header_link" href="list.php">管理者画面
            </a></li>
            <li><a class="header_link" href="admin_logout.php">ログアウト</a></li>
            </ul>
        </div>
    </div>
    <!--ヘッダー終了-->
    <!--コンテンツ開始-->
    <div id="top_content">
        <h2>登録者情報</h2>
        <!--tableで全ての情報一覧を取得する-->
        <table style="margin : 0 auto">
            <tr>
                <td>登録者ID</td>
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
            echo '<td>'.htmlspecialchars($member['id']).' '.'</td>';
            echo '<td>'.htmlspecialchars($member['name']).' '.'</td>';
            echo '<td>'.htmlspecialchars($member['email']).' '.'</td>';
            echo '<td>'.htmlspecialchars($member['p_language']).' '.'</td>';
            echo '<td>'.htmlspecialchars($member['sex']).' '.'</td>';
            echo '<td>'.htmlspecialchars($member['age']).'   '.'</td>';
            /*編集ボタンも、URLパラメーターを使ってforeach文の中にいれる。*/
           /* URLパラメータを使って、該当IDのedit.phpに遷移する*/
            echo '<td><a class="button_link" href="edit.php?page_list='.htmlspecialchars($member['id']).'">編集</a>'.'  '.'</td>';
            /*詳細ボタンも、URLパラメーターを使ってこの中にいれこむ*/
            echo '<td><a class="button_link" href="list_delete.php?delete_list='.htmlspecialchars($member['id']).'">削除</a></td>';
            echo '</tr>';
            echo '<br>';
        }?>
        </table>
        <br>
        <br>
        <div id="button-center">
            <ul>
            <li><a class="button_link2" href="list.php">登録者情報一覧画面に戻る</a></li>
            <li><a class="button_link2" href="admin_logout.php">ログアウト</a></li>
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