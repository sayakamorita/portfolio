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
/*スレッドを投稿してきたあと、トップ画面からまたスレッドを立てるボタンを押下されたとき、前回のスレッド投稿フラグが残っているので、それを折る*/
if(empty($_POST) && isset($_SESSION['create_thread']['flg'])){
    unset($_SESSION['create_thread']['flg']);
}
/*
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

/*各項目の空欄かどうかエラーチェック*/
if(!empty($_POST)){
    $_SESSION['create_thread'] = $_POST;
    if($_POST['question_title'] ===''){
        $error['question_title'] = 'blank';
    }
    if($_POST['p_question'] ===''){
        $error['p_question'] = 'blank';
    }
    if(strlen($_POST['question_title']) > 90){
        $error['question_title'] = 'length';
    }
    /*エラーがなかったらスレッド確認画面に遷移する*/
    if(empty($error)){
        header('Location:create_thread_confirm.php');
        exit();
    }
}

/*urlパラメーターにaction_thread=rewrite_threadと表示されていたら、フォームにセッションに保存された値（パスワード以外）を出力する→edit_check.phpで「戻る」ボタンを押下して遷移してきた時の処理。*/
if($_REQUEST['action_thread'] == 'rewrite_thread' && isset($_SESSION['create_thread'])){
    $_POST['question_title'] = $_SESSION['create_thread']['question_title'];
    $_POST['p_question'] = $_SESSION['create_thread']['p_question'];
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>PHP質問スレッド一覧画面</title>
	<link rel="stylesheet" href="style.css" />
</head>
<body>
<div id="wrap">
    <!--ヘッダー開始-->
    <div class="head">
        <div id="head-left">
        <h1><a class="header_title" href="top_p.php?flg=on">初心者エンジニアのための質問掲示板</a></h1>
        </div>
        <div id="head-right">
            <ul>
            <li><a class="header_link" href="top_p.php?flg=on">トップページ</a></li>
                <li><a class="header_link" href="logout_p.php">ログアウト</a></li>
            </ul>
        </div>
    </div>
    <!--ヘッダー終了-->
    <!--コンテンツ開始-->
    <div id="content">
        <h2>スレッド作成画面</h2>
        <div class="">
            <form action="" method="post">
            <!--hiddenで、トップ画面まで投稿後のセッションをもたせておく-->
            <input type="hidden" name="flg" value="1">
            <!--カテゴリーを選ぶ-->
            <p>質問する言語を選択してください
                <select name='language_category'>
                <!--カテゴリー選択Java-->
                <option value='Java' 
                        <?php if($_REQUEST['action_thread'] == 'rewrite_thread' && $_SESSION['create_thread']['language_category'] === 'Java' || $_SESSION['create_thread']['language_category'] === 'Java'){
                            echo 'selected';
                               }  
                        ;?>>Java</option>
                <!--カテゴリー選択JavaScript-->
                <option value='JavaScript' 
                        <?php if($_REQUEST['action_thread'] == 'rewrite_thread' && $_SESSION['create_thread']['language_category'] === 'JavaScript' || $_SESSION['create_thread']['language_category'] === 'JavaScript'){
                            echo 'selected';
                               }  
                        ;?>>JavaScript</option>
                <!--カテゴリー選択PHP-->
                <option value='PHP' 
                        <?php if($_REQUEST['action_thread'] == 'rewrite_thread' && $_SESSION['create_thread']['language_category'] === 'PHP' || $_SESSION['create_thread']['language_category'] === 'PHP'){
                            echo 'selected';
                               }  
                        ;?>>PHP</option>
                <!--カテゴリー選択Ruby-->
                <option value='Ruby' 
                        <?php if($_REQUEST['action_thread'] == 'rewrite_thread' && $_SESSION['create_thread']['language_category'] === 'Ruby' || $_SESSION['create_thread']['language_category'] === 'Ruby'){
                            echo 'selected';
                               }  
                        ;?>>Ruby</option>
                <!--カテゴリー選択Python-->
                <option value='Python' 
                        <?php if($_REQUEST['action_thread'] == 'rewrite_thread' && $_SESSION['create_thread']['language_category'] === 'Python' || $_SESSION['create_thread']['language_category'] === 'Python'){
                                    echo 'selected';
                                    }  
                        ;?>>Python</option>
                <!--カテゴリー選択HTML/CSS-->
                <option value='HTML/CSS' 
                        <?php if($_REQUEST['action_thread'] == 'rewrite_thread' && $_SESSION['create_thread']['language_category'] === 'HTML/Css' || $_SESSION['create_thread']['language_category'] === 'HTML/CSS'){
                            echo 'selected';
                               }  
                        ;?>>HTML/CSS</option>
                <!--カテゴリー選択Laravel-->
                <option value='Laravel' 
                        <?php if($_REQUEST['action_thread'] == 'rewrite_thread' && $_SESSION['create_thread']['language_category'] === 'Laravel' || $_SESSION['create_thread']['language_category'] === 'Laravel'){
                                    echo 'selected';
                               }  
                        ;?>>Laravel</option>
                <!--カテゴリー選択Ruby on Rails-->
                    <option value='Ruby on Rails' 
                        <?php if($_REQUEST['action_thread'] == 'rewrite_thread' && $_SESSION['create_thread']['language_category'] === 'Ruby on Rails' || $_SESSION['create_thread']['language_category'] === 'Ruby on Rails'){
                                echo 'selected';
                               }  
                        ;?>>Ruby on Rails</option>
                </select></p>
                <p>タイトルを入力してください。(30文字以内)：<input type="text" size="60" name="question_title" placeholder="Javaのオブジェクト指向について" value="<?php print(htmlspecialchars($_POST['question_title'],ENT_QUOTES));?>"></p>
                    <?php if($error['question_title'] === 'blank'):?>
                        <p class="error">*タイトルを入力してください。</p>
                    <?php endif;?>
                    <?php if($error['question_title'] === 'length'):?>
                        <p class="error">*タイトルは30文字以内で入力してください。</p>
                    <?php endif;?>    
                <p>投稿者：<?php print(htmlspecialchars($member['name']));?></p>
                <p>質問内容(*できるだけ詳しく入力してください。)</p>
                    <textarea name="p_question" cols="130" rows="20" placeholder = "質問内容を入力してください。"><?php print(htmlspecialchars($_POST['p_question'],ENT_QUOTES));?></textarea>
                    <?php if($error['p_question'] === 'blank'):?>
                        <p class="error">*質問内容を入力してください。</p>
                    <?php endif;?>
                <p><input class="button_link" type="submit" value="確認画面へ"></p>
            </form>
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
