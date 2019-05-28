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

/*ログイン画面からトップ画面に遷移した際のhiddenタグを消去する*/
if(isset($_SESSION['login'])){
    unset($_SESSION['login']);
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
/*DBから投稿を取得する
$former_questions = $db->query('SELECT * FROM thread');
$former_questions->execute();
$former_question = $former_questions->fetch();
*/
/*スレッドを立てる処理
laravelに関するスレッドを立てるページなので、thread_id='laravel'に指定する。*/
if(!empty($_POST)){
    if($_POST['name'] !== '' && $_POST['p_question'] !== ''){
        $questions = $db->prepare('INSERT INTO thread SET title = ?,member_name = ?, message = ?, created = NOW(),thread_id ="laravel",p_language="Laravel"');
        $questions->execute(array($_POST['title'],$member['name'],$_POST['p_question']));
        header('Location:thread_laravel.php');
        exit();
    }
}

/*DBから、今何件スレッドが立っているか、数をとってくる*/
$count = $db->query('SELECT COUNT(*) as cnt FROM thread WHERE thread_id = "laravel"');
$cnt = $count->fetch();

/*ページネーションの処理*/
$page = $_REQUEST['page_thread'];
if($page == ''){
    $page = 1;
}
$page = max($page,1);
$maxPage = ceil($cnt['cnt'] /10);
$page = min($page,$maxPage);
$start = ($page -1)*10;
/*DBから投稿を取得する*/
$threads = $db ->prepare('SELECT * FROM thread WHERE thread_id = "laravel" ORDER BY modified LIMIT ?,10');
$threads->bindParam(1,$start,PDO::PARAM_INT);
$threads->execute();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Laravel 質問スレッド一覧画面</title>
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
                <li><a class="header_link" href="logout_p.php">ログアウト</a></li>
            </ul>
        </div>
    </div>
    <!--ヘッダー終了-->
    <!--コンテンツ開始-->
    <div id="content">
        <h2>Laravel 初心者質問掲示板</h2>
        <h3>最新の投稿</h3>
        <hr>
        <table class="question">
        <?php 
        foreach($threads as $thread){
            echo '<tr>';
            echo '<td>タイトル：'.htmlspecialchars($thread['title']).'｜'.'</td>';
            echo '<td>投稿者'.htmlspecialchars($thread['member_name']).'／'.'</td>';
            echo '<td>更新日時'.htmlspecialchars($thread['modified']).'／'.'</td>';
            /*詳細ボタンを、URLパラメーターを使ってforeach文の中にいれる。*/
           /* URLパラメータを使って、該当IDのeach_thread.phpに遷移する*/
            /*echo '<td><a href="?page_thread='.htmlspecialchars($thread['id']).'">詳細</a>'.'  '.'</td>';*/

            /*threadテーブルのidと、each_threadのthread_idをあわせる。*/
            echo '<td><a class="button_link" href="each_thread.php?thread_id='.htmlspecialchars($thread['id']).'">詳細</a>'.'  '.'</td>';
            if($thread['member_name'] === $member['name']){
                echo '<td><a class="button_link" href="delete_thread.php?thread_id='.htmlspecialchars($thread['id']).'">削除</a>'.'  '.'</td>';}
            echo '</tr>';
        };?>
        </table>
        <hr>
        <ul class="paging">
            <?php if($page >1):?>
                <li><a href="thread.php?page_thread=<?php print(htmlspecialchars($page -1));?>">前のページへ</a></li>
            <?php else:?>
                <li>前のページへ</li>
            <?php endif;?>
            <?php if($page < $maxPage):?>
                <li><a href="thread.php?page_thread=<?php print(htmlspecialchars($page+1));?>">次のページへ</a></li>
            <?php else:?>
                <li>次のページへ</li>    
            <?php endif;?>
        </ul>
        <h4>質問はこちらから</h4>
        <form action="" method="post">
            <p>タイトル：<input type="text" name="title" placeholder="タイトル"></p>
            <p>投稿者：<?php print(htmlspecialchars($member['name']));?></p>
            <p>質問内容</p>
                <textarea name="p_question" cols="100" rows="10" placeholder = "質問内容を入力してください。"></textarea>
            <p><input class="button_link2" type="submit" value="スレッドを立てる"></p>
        </form>
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
