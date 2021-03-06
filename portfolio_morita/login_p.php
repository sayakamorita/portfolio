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

/*クッキーが空ではなかったら→*/
if($_COOKIE['email'] !== ''){
    $email = $_COOKIE['email'];
}

/*ログイン状態だったら、top_p.phpにとぶ*/
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
    $_SESSION['time'] = time();
    header('Location:top_p.php');
    exit();
}

/*配列POSTの値がからでなければ→つまり、formタグないのsubmitボタンがクリックされたあとの処理を記述できる*/
if(!empty($_POST)){
/*   $emailという変数に、ログインボタンで入力されたメールアドレスを格納する*/
    $email = $_POST['email'];
/*入力されたメールアドレスとパスワードが空白でなかったら*/
    if($_POST['email'] !== '' && $_POST['password'] !== ''){
/*$loginという変数に、データベースから取ってきた値を格納する*/
        $login = $db ->prepare('SELECT * FROM members WHERE email = ? AND password = ?');
/*上記の?に、ログイン画面で入力されたメールアドレスとパスワードを格納する*/
        $login ->execute(array($_POST['email'],sha1($_POST['password'])));
/*$memberという変数に、メールアドレスとパスワードが合致したデータをfetchメソッドで取り出し、格納する*/
        $member =$login ->fetch();
        /*ログインできるとわかったら、セッションにDBから取り出したIDとログインした時間を記録する*/
        /*合致するデータがあった場合*/
        if($member){
            /*セッションidにログインした人のidを格納する*/
            $_SESSION['id'] = $member['id'];
            $_SESSION['name'] = $member['name'];
            /*セッションtimeにログインした時間を格納する*/
            $_SESSION['time'] = time();
            /*ログインするときに「次回からは自動的にログインする」にチェックが入っていた場合*/
            $_SESSION['login'] = $_POST['login'];
            if($_POST['save'] === 'on'){
            /*emailというクッキーにログイン時に入力されたメールアドレスを保存する。その期間は3日間とする*/
                setcookie('email',$_POST['email'],time() + 60*60*24*3);
            }
        /*ログインが完了したら、再度トップページ(top_p.php)に遷移する*/
            header('Location:top_p.php');
            exit();
        }else{
            /*ログインに失敗したら、$errorという配列の['login']に'failed'を格納する*/
            $error['login'] ='failed';
        }
    }    
}
/*ログインボタンが押された時に、メールアドレスが入力されていなかった場合の処理*/
if($_POST['email'] ===''){
    $error['email'] = 'blank';
}
/*ログインボタンが押された時にパスワードが入力されていなかった場合の処理*/
if($_POST['password'] ===''){
    $error['password'] = 'blank';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ログイン画面</title>
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
            <li class="nav-item"><a class="nav-link" href="admin.php">管理者ログイン</a></li>
            <li class="nav-item"><a class="nav-link" href="index1_p.php">ユーザー登録</a></li>
        </ul>
        </div>
    </nav>
<!--ヘッダー終了-->
    <!--コンテンツ開始-->
    <div id="top_content">
        <h2>ログイン画面</h2>
        
        <!--formタグのアクションは、ログイン時に入力がされておらずエラーになったとき、再度呼び出されるので、空白にしておく-->
        <form action="" method="post">
        <!--valueには、セッションに登録されたemailを出力する。（呼び出された時は、$_COOKIE['email']に値がない限り空白が表示される)-->
            <p>メールアドレス：<input type="text" name="email" value="<?php print(htmlspecialchars($email,ENT_QUOTES));?>"></p>
        <!--上記で定義した、メールアドレスが空欄でログインボタンが押された場合の条件と処理-->
                <?php if($error['email'] === 'blank'):?>
                <p class="error">*メールアドレスを入力してください。</p>
                <?php endif;?>
        <!--valueには、セッションに登録されたpasswordを出力する。-->
            <p>パスワード：<input type="password" name="password" value="<?php print(htmlspecialchars($_POST['password'],ENT_QUOTES));?>"></p>
        <!--上記で定義した、パスワードが空欄でログインボタンが押された場合の条件と処理-->
                <?php if($error['password'] === 'blank'):?>
                <p class="error">*パスワードを入力してください。</p>
                <?php endif;?>
        <!--$memberが存在しなかった場合、つまりDBから合致する値が取ってこれなかった場合は、ログインに失敗する。-->
                <?php if($error['login'] === 'failed'):?>
                <p class="error">*メールアドレスかパスワードが正しくありません。</p>
                <?php endif;?>
        <!--下記にチェックが入っていたら、setcookie('email',$_POST['email'],time() + 60*60*24*3)という処理が行われる-->
            <p><input type="checkbox" id="save" name="save" value="on">
            <label for="save">次回からは自動的にログインする</label></p>
            <input type="hidden" name="login" value="1">
            
            <div class="button_center">
                <ul>
                    <li><input class="button_link" type="submit" value="ログイン" ></li>
                    <li><input class="button_link" type="button" onclick="location.href='../index1_p.php'" value="新規ユーザー登録"></li>
                </ul>
            </div>
        </form>
        <h3>ゲストユーザーアカウントはこちら</h3>
        <div id="login_guest">
            <ul >
                <li>メールアドレス：guest｜</li>
                <li>パスワード：guest</li>
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