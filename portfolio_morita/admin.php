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

/*クッキーがからではなかったら*/
if($_COOKIE['admin'] !== ''){
    $admin_id = $_COOKIE['admin'];
}

/*管理者ログイン状態だったら、list.phpにとぶ*/
if(isset($_SESSION['admin_id']) && $_SESSION['time'] + 3600 > time()){
    $_SESSION['time'] = time();
    header('Location:list.php');
    exit();
}
/*ログイン画面からトップ画面に遷移した際のhiddenタグを消去する*/
if(isset($_SESSION['login'])){
    unset($_SESSION['login']);
}
/*スレッドをたてた時に持っているhiddenでもっているflgを消去する*/
if(isset($_SESSION['create_thread']['flg'])){
    unset($_SESSION['create_thread']['flg']);
}

/*ログインボタンがクリックされた時の処理*/
if(!empty($_POST)){
    /*$passwordという変数に、admin.phpで入力されたパスワードを代入する*/
    $admin_id = $_POST['admin_id'];
    /*秘密の質問の答えとパスワードが空ではなかったら*/
    if($_POST['admin_id'] !=='' && $_POST['password'] !== ''){ 
        /*$adminsという変数に、データベースから取ってきた値を格納する*/
        $admins = $db ->prepare('SELECT * FROM admin WHERE admin_id = ?AND password = ?');
        /*上記の?に、管理者画面で入力されたパスワードを格納する*/
        $admins->execute(array($_POST['admin_id'],$_POST['password']));
        /*$answerという変数に、パスワードが合致したデータをfetchメソッドで取り出し、格納する*/
        $admin = $admins->fetch();

        /*合致するデータがあった場合*/
        if($admin){
            /*セッションidにログインした人（管理人）のidを格納する*/
            $_SESSION['admin_id'] = $admin['admin_id'];
            /*セッションtimeにログインした時間を格納する*/
            $_SESSION['time'] = time();
            /*ログインするときに「次回からは自動的にログインする」にチェックが入っていた場合*/
            if($_POST['save'] === 'on'){
            /*adminというクッキーにログイン時に入力されたパスワードを保存する。その期間は1時間とする*/
                setcookie('admin',$_POST['admin_id'],time() + 60*60*1);
            }
        /*ログインが完了したら、再度トップページ(top_p.php)に遷移する*/
            header('Location:list.php');
            exit();
        }else{
            /*ログインに失敗したら、$errorという配列の['login']に'failed'を格納する*/
            $error['login'] ='failed';
        }
    }       
}

    if($_POST['admin_id'] === ''){
        $error['admin_id'] = 'blank';
    }
    if($_POST['password'] === ''){
        $error['password'] = 'blank';
    }

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>管理者ログイン画面</title>
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
            <li class="nav-item active"><a class="nav-link" href="../top_p.php">トップページ</a></li>
        </ul>
        </div>
    </nav>
<!--ヘッダー終了-->
    <!--コンテンツ開始-->
    <div id="top_content">
        <h2>管理者ログインページ</h2>
            <form action="" method="post">
            <p>管理者ID：
                <input type="text" name="admin_id" value="<?php print(htmlspecialchars($admin_id,ENT_QUOTES));?>"></p>
                    <?php if($error['admin_id'] === 'blank'):?>
                        <p class="error">*管理者IDを入力してください。</p>
                    <?php endif;?>    
            <p>管理者パスワード：
                <input type="password" name="password" value=""></p>
                <?php if($error['password'] === 'blank'):?>
                            <p class="error">*パスワードを入力してください</p>
                <?php endif;?>

                <?php if($error['login'] === 'failed'):?>
                    <p class="error">*管理者IDかパスワードが正しくありません。</p>
                <?php endif;?>

                <p><input type="checkbox" id="save" name="save" value="on">
                <label for="save">次回からは自動的にログインする</label></p>
                <input class="button_link" type="submit" value="管理者としてログイン">
            </form>
            <!--コンテンツ終了-->
    </div>
    <!--フッター終了-->
    <div class="footer">
        <p>©︎2019 morita</p>
    </div>
    <!--フッター終了-->
</div>
</body>
</html>
