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

/*ユーザーのログイン情報があった場合の処理
セッションにidが登録されているということは、ログイン状態であるということ。*/
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
    $_SESSION['time'] = time();
    $members = $db ->prepare('SELECT * FROM members WHERE id = ?');
    $members ->execute(array($_SESSION['id']));
    $member = $members ->fetch();
}else{
    /*ユーザーのログイン情報がなかった場合の処理*/
    header('Location:login_p.php');
    exit();
}

/*回答内容があるかどうかのエラーチェック*/
if($_POST['each_answer'] === ''){
    $error['each_answer'] = 'blank';
}
/*DBのthreadテーブルから、idが$thread['id']のものをとりだす*/
$each_messages = $db->prepare('SELECT * FROM thread WHERE id = ?');
/*list.phpの編集ボタンのURLパラメーターthread_idでおくられてきた数字を、上のSQL文のid＝？に代入して値を取り出す*/
$each_messages ->execute(array($_REQUEST['thread_id']));
$each_message = $each_messages->fetch();

/*DBのeach_threadテーブルからidが$_REQUEST['thread_id']のものを取り出す。(表示させるため)*/
$answers = $db ->prepare('SELECT * FROM each_thread WHERE thread_id = ? ORDER BY modified');
$answers->execute(array($_REQUEST['thread_id']));

/*返信する処理
each_threadテーブルに、このスレッドのid,投稿者名,回答内容を格納する。*/
if(!empty($_POST)){
    if($_POST['each_answer'] !== ''){
        $each_answers = $db->prepare('INSERT INTO each_thread SET thread_id = ?, member_name = ?, message= ?, created= NOW()');
/*thread_idには、thread.phpからもってきた、このスレッドのidを格納する*/
        $each_answers->execute(array($_REQUEST['thread_id'],$member['name'],$_POST['each_answer']));
        header('Location:each_thread.php?thread_id='.$_REQUEST['thread_id']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>質問スレッド一覧画面</title>
	<link rel="stylesheet" href="style.css" />
    <!--bootstrap読み込み-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script type="text/javascript" src="portfolio.js"></script>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
</head>
<body>
<div id="wrap">
     <!--ヘッダー開始-->
     <nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#000000;">
        <a class="navbar-brand" href="top_p.php"></a>   
    <button class="navbar-toggler" data-toggle="collapse" data-target=#navbarNav>
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse navbar-right" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active"><a class="nav-link" href="top_p.php">トップページ</a></li>
            <li class="nav-item"><a class="nav-link" href="create_thread.php">スレッドを立てる</a></li>
            <li class="nav-item"><a class="nav-link" href="logout_p.php">ログアウト</a></li>
        </ul>
    </div>
    </nav>
    <!--ヘッダー終了-->
    <!--コンテンツ開始-->
    <div id="content">
        <div class="each_thread">
            <h2>
                <?php print(htmlspecialchars($each_message['p_language'],ENT_QUOTES));?>初心者質問掲示板（詳細）
            </h2>
            <div class="table_center">
                    <!--thread.phpで選択したスレッドの質問内容（タイトル、投稿者、質問内容、投稿日時）を最上部に表示する-->
                <div class="thread_content">
                        <h3>タイトル：<?php print(htmlspecialchars($each_message['title'],ENT_QUOTES));?>
                        </h3>
                        <h4>投稿者：<?php print(htmlspecialchars($each_message['member_name'],ENT_QUOTES));?>
                        </h4>
                         <h4>質問内容</h4>
                        <p><?php print(htmlspecialchars($each_message['message'],ENT_QUOTES));?></p>
                        <h4>投稿日時：<?php print(htmlspecialchars($each_message['created'],ENT_QUOTES));?></h4>
                </div>
                <hr>
                <div class="thread_content">
                        <?php 
                        foreach($answers as $answer){
                            echo '<h4>【返信内容】</h4>';
                            echo '<p style="word-break : break-all">'.nl2br(htmlspecialchars($answer['message'])).'</p>';
                            echo '<h4>投稿者：'.htmlspecialchars($answer['member_name']).'</h4>';
                            echo '<h4><b>更新日時：'.htmlspecialchars($answer['modified']).'</h4>';
                            echo '<hr>';
                        };?>
                </div>
                <div class="reply">
                    <h4>この質問に回答する</h4>
                    <form action="" method="post">
                        <p>投稿者：<?php print(htmlspecialchars($member['name'],ENT_QUOTES));?></p>
                        <textarea name="each_answer" cols="80" rows="15" placeholder = "回答を入力してください。"></textarea>
                        <br>
                        <?php if($error['each_answer'] === 'blank'):?>
                            <p class="error">*回答を入力してください。</p>
                    <?php endif;?>
                        <br>
                        <div id="button_center">
                                <input class="button_link3" type="submit" value="回答する"> 
                        </div>
                    </form>
                    <br>
                </div>
            </div> 
            <br>
            <a class="button_link3" href="thread_<?php print($each_message['thread_id']);?>.php">スレッド一覧に戻る</a>
        </div>
    </div>
    <br>
    <!--コンテンツ終了-->
    <!--フッター終了-->
    <div class="footer">
        <p>©︎2019 morita</p>
    </div>
    <!--フッター終了-->
</div>
</body>
</html>
