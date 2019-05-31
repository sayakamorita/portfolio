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

/*カテゴリーでJavaを選択された時の処理*/
    if($_SESSION['create_thread']['language_category'] === 'Java'){
    $statement = $db ->prepare('INSERT INTO thread SET title=?, thread_id = "java", p_language = "Java", message = ?, member_name=?, created=NOW()');
    /*sql文の?の部分にセッション配列に登録されたそれぞれの値を代入する*/
    $statement ->execute(array
    ($_SESSION['create_thread']['question_title'],
    $_SESSION['create_thread']['p_question'],
    $member['name'])
    );
    /*hiddenタグだけ残しておく。*/
    unset($_SESSION['create_thread']['language_category']);
    unset($_SESSION['create_thread']['question_title']);
    unset($_SESSION['create_thread']['p_question']);
    header('Location:top_p.php');
    exit();
}
/*カテゴリーでJavaScriptを選択された時の処理*/
if($_SESSION['create_thread']['language_category'] === 'JavaScript'){
    $statement = $db ->prepare('INSERT INTO thread SET title=?, thread_id = "js", p_language = "JavaScript", message = ?, member_name=?, created=NOW()');
    /*sql文の?の部分にセッション配列に登録されたそれぞれの値を代入する*/
    $statement ->execute(array
    ($_SESSION['create_thread']['question_title'],
    $_SESSION['create_thread']['p_question'],
    $member['name'])
    );
    /*hiddenタグだけ残しておく。*/
    unset($_SESSION['create_thread']['language_category']);
    unset($_SESSION['create_thread']['question_title']);
    unset($_SESSION['create_thread']['p_question']);
    header('Location:top_p.php');
    exit();
}
/*カテゴリーでPHPを選択された時の処理*/
if($_SESSION['create_thread']['language_category'] === 'PHP'){
    $statement = $db ->prepare('INSERT INTO thread SET title=?, thread_id = "php", p_language = "PHP", message = ?, member_name=?, created=NOW()');
    /*sql文の?の部分にセッション配列に登録されたそれぞれの値を代入する*/
    $statement ->execute(array
    ($_SESSION['create_thread']['question_title'],
    $_SESSION['create_thread']['p_question'],
    $member['name'])
    );
    /*hiddenタグだけ残しておく。*/
    unset($_SESSION['create_thread']['language_category']);
    unset($_SESSION['create_thread']['question_title']);
    unset($_SESSION['create_thread']['p_question']);
    header('Location:top_p.php');
    exit();
}
/*カテゴリーでRubyを選択された時の処理*/
if($_SESSION['create_thread']['language_category'] === 'Ruby'){
    $statement = $db ->prepare('INSERT INTO thread SET title=?, thread_id = "ruby", p_language = "Ruby", message = ?, member_name=?, created=NOW()');
    /*sql文の?の部分にセッション配列に登録されたそれぞれの値を代入する*/
    $statement ->execute(array
    ($_SESSION['create_thread']['question_title'],
    $_SESSION['create_thread']['p_question'],
    $member['name'])
    );
    /*hiddenタグだけ残しておく。*/
    unset($_SESSION['create_thread']['language_category']);
    unset($_SESSION['create_thread']['question_title']);
    unset($_SESSION['create_thread']['p_question']);
    header('Location:top_p.php');
    exit();
}
/*カテゴリーでPythonを選択された時の処理*/
if($_SESSION['create_thread']['language_category'] === 'Python'){
    $statement = $db ->prepare('INSERT INTO thread SET title=?, thread_id = "python", p_language = "Python", message = ?, member_name=?, created=NOW()');
    /*sql文の?の部分にセッション配列に登録されたそれぞれの値を代入する*/
    $statement ->execute(array
    ($_SESSION['create_thread']['question_title'],
    $_SESSION['create_thread']['p_question'],
    $member['name'])
    );
    /*hiddenタグだけ残しておく。*/
    unset($_SESSION['create_thread']['language_category']);
    unset($_SESSION['create_thread']['question_title']);
    unset($_SESSION['create_thread']['p_question']);
    header('Location:top_p.php');
    exit();
}
/*カテゴリーでHTML/CSSを選択された時の処理*/
if($_SESSION['create_thread']['language_category'] === 'HTML/CSS'){
    $statement = $db ->prepare('INSERT INTO thread SET title=?, thread_id = "html_css", p_language = "HTML/CSS", message = ?, member_name=?, created=NOW()');
    /*sql文の?の部分にセッション配列に登録されたそれぞれの値を代入する*/
    $statement ->execute(array
    ($_SESSION['create_thread']['question_title'],
    $_SESSION['create_thread']['p_question'],
    $member['name'])
    );
    /*hiddenタグだけ残しておく。*/
    unset($_SESSION['create_thread']['language_category']);
    unset($_SESSION['create_thread']['question_title']);
    unset($_SESSION['create_thread']['p_question']);
    header('Location:top_p.php');
    exit();
}
/*カテゴリーでLaravelを選択された時の処理*/
if($_SESSION['create_thread']['language_category'] === 'Laravel'){
    $statement = $db ->prepare('INSERT INTO thread SET title=?, thread_id = "laravel", p_language = "Laravel", message = ?, member_name=?, created=NOW()');
    /*sql文の?の部分にセッション配列に登録されたそれぞれの値を代入する*/
    $statement ->execute(array
    ($_SESSION['create_thread']['question_title'],
    $_SESSION['create_thread']['p_question'],
    $member['name'])
    );
    /*hiddenタグだけ残しておく。*/
    unset($_SESSION['create_thread']['language_category']);
    unset($_SESSION['create_thread']['question_title']);
    unset($_SESSION['create_thread']['p_question']);
    header('Location:top_p.php');
    exit();
}
/*カテゴリーでRuby on Railsを選択された時の処理*/
if($_SESSION['create_thread']['language_category'] === 'Ruby on Rails'){
    $statement = $db ->prepare('INSERT INTO thread SET title=?, thread_id = "rails", p_language = "Ruby on Rails", message = ?, member_name=?, created=NOW()');
    /*sql文の?の部分にセッション配列に登録されたそれぞれの値を代入する*/
    $statement ->execute(array
    ($_SESSION['create_thread']['question_title'],
    $_SESSION['create_thread']['p_question'],
    $member['name'])
    );
    /*hiddenタグだけ残しておく。*/
    unset($_SESSION['create_thread']['language_category']);
    unset($_SESSION['create_thread']['question_title']);
    unset($_SESSION['create_thread']['p_question']);
    header('Location:top_p.php');
    exit();
}
   
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
            <h1>初心者エンジニアのための質問掲示板</h1>
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
