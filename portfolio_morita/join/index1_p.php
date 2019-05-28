<?php
/*ユーザー情報入力フォームで入力された値は、$_SESSION['join'](joinという名前の2次元配列)に格納される。*/
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
/*フォームが送信されているかどうかを確認するには、$_POSTという配列が空かどうかを調べればよい
  if(!empty($_POST))とすることで、フォームが空ではなかった場合のエラーチェックを行うことができる*/
if(!empty($_POST)){
/*formのname属性(name)が入力されていなかったときの処理*/
if($_POST['name'] === ''){
    /*$errorという配列のnameの中に、blankという何も入力されていないエラー状況を格納
      これを条件として、入力されていなかった時のエラーチェックをif文で行うことができる。*/
    $error['name'] = 'blank';
}
/*ニックネームのバリデーションチェック*/

if($_POST['email'] === ''){
    $error['email'] = 'blank';
}
/*メールアドレスのバリデーションチェック*/

/*パスワードが4文字以下の場合のエラーチェック*/
if(strlen($_POST['password']) < 4){
    $error['password'] = 'length';
}
if($_POST['password'] === ''){
    $error['password'] = 'blank';
}
/*プログラミング言語が未入力の場合のエラーチェック*/
if(empty($_POST['p_language'])){
    $error['p_language'] = 'blank';
    }

$fileName = $_FILES['image']['name'];
if(!empty($fileName)){
    $ext = substr($fileName, -3);
    if($ext != 'jpg' && $ext != 'gif' && $ext != 'png'){
        $error['image'] = 'type';
    }
}

//アカウントが重複していないかをチェック
if(empty($error)){
    $member = $db ->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email = ?');
    $member ->execute(array($_POST['email']));
    $record = $member ->fetch();
    if($record['cnt'] >0){
        $error['email'] = 'duplicate';
    }
}
/*すべてのエラーチェックが完了し、エラーがない状態であればcheck.phpに遷移するようにする*/
if(empty($error)){
    $image = date('YmdHis').$_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'],'/var/www/html/member_picture_p'.$image);
    $_SESSION['join']['image'] = $image;
    /*エラーチェックが完了したあとに、joinという名前のセッションに$_POST（formで送信されてきた値）を格納する処理*/
    $_SESSION['join'] = $_POST;
    /*プログラミング言語が選択されている場合、「,」で区切って確認画面で表示させる*/
    if(isset($_POST['p_language'])){
        $p_language = implode(" , ",$_POST['p_language']);
    }
    $_SESSION['join']['p_language'] = $p_language;
    
    header('Location:check_p.php');
exit();
    }
}
/*urlパラメーターにaction=rewriteと表示されていたら、フォームにセッションに保存された値（パスワード以外）を出力する→check.phpで「戻る」ボタンを押下して遷移してきた時の処理。*/
if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){
    $_POST['name'] = $_SESSION['join']['name'];
    $_POST['email'] = $_SESSION['join']['email'];
    $_POST['image'] = $_SESSION['join']['image'];
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ユーザー登録</title>
    <link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
<!--ヘッダー開始-->
    <div class="head">
        <div id="head-left">
        <h1><a class="header_title" href="../top_p.php">初心者エンジニアのための質問掲示板</a></h1>
        </div>
        <div id="head-right">
            <ul>
            <?php if(!isset($_SESSION['id'])):?>
                    <li><a class="header_link" href="../login_p.php">ログイン</a></li>
                <?php endif;?>
                <li><a class="header_link" href="../admin.php">管理者ログイン</a></li>
            </ul>
        </div>
    </div>
<!--ヘッダー終了-->
<!--入力フォーム開始-->
    <div id="top_content">
        <h2>新規ユーザー登録</h2>
        <h3>ユーザー情報を入力してください。</h3>
        <!--formタグで囲まれたものは、method属性で指定された$_POSTという【2次元配列】の中に格納される。-->
        <!--formのアクション属性を空にしているのは、同じ画面を再び呼び出すため。（正しくログインできたときだけページ遷移し、エラーがでたときは再度この画面をよびだす。-->
        <form action="" method="post" enctype="multipart/form-data">
        <!-- value属性には、ここで入力した値(postされた値)を設定して出力する-->
            <p>ニックネーム：<input type="text" name="name" placeholder ="山田太郎" value="<?php print(htmlspecialchars($_POST['name'],ENT_QUOTES));?>"/>
            
                <?php if($error['name'] === 'blank'):?>
                <p class="error">*ニックネームを入力してください。</p>
                <?php endif;?>
            </p>   
            <!--check.phpで「戻る」ボタンを押下して遷移してきた時、セッションに登録されたvalue値でラジオボタンcheckedの場合分けを行う-->
            <div class="radio">性別：
                <input type="radio" value="男性" name="sex" id="man"  
                    <?php if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){
                        if($_SESSION['join']['sex'] === '男性'){
                            echo 'checked';
                        }
                    };?>
                    <?php if($_POST['sex'] === '男性'){
                            echo 'checked';
                    };?>
                    <?php if($_POST['join']['sex'] === null){
                        echo 'checked';
                    };?>>
                <label for="radio02-01">男性</label>
                <input type="radio" value="女性" name="sex" id="woman"  
                <?php if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){
                        if($_SESSION['join']['sex'] === '女性'){
                            echo 'checked';
                        }
                    }?>
                    <?php if($_POST['sex'] === '女性'){
                            echo 'checked';
                    };?>>
                <label for="radio02-01">女性</label>
                <input type="radio" value="選択しない" name="sex" id="other"  
                <?php if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){
                        if($_SESSION['join']['sex'] === '選択しない'){
                            echo 'checked';
                        }
                    }?>
                    <?php if($_POST['sex'] === '選択しない'){
                            echo 'checked';
                    };?>>
                <label for="radio02-01">選択しない</label> 
            </div>
            <p>年齢：</p>
                <select name='age'>
                <option value='' disabled style='display:none;'>選択してください</option>
                    <option value='10代' 
                        <?php if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){
                            if($_SESSION['join']['age'] === '10代'){
                            echo 'selected';
                        }
                    }
                    ;?>
                    <?php if($_POST['age'] === '10代'){
                            echo 'selected';
                    };?>>10代</option>
                    <option value='20代' 
                        <?php if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){
                            if($_SESSION['join']['age'] === '20代'){
                            echo 'selected';
                        }}
                    ;?>
                    <?php if($_POST['age'] === '20代'){
                            echo 'selected';
                    };?>>20代</option>
                    <option value='30代' 
                        <?php if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){
                            if($_SESSION['join']['age'] === '30代'){
                            echo 'selected';
                        }}
                    ;?>
                    <?php if($_POST['age'] === '30代'){
                            echo 'selected';
                    };?>>30代</option>
                    <option value='40代' 
                        <?php if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){
                            if($_SESSION['join']['age'] === '40代'){
                            echo 'selected';
                        }}
                    ;?>
                    <?php if($_POST['age'] === '40代'){
                            echo 'selected';
                    };?>>40代</option>
                    <option value='50代' 
                        <?php if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){
                            if($_SESSION['join']['age'] === '50代'){
                            echo 'selected';
                        }}
                    ;?>
                    <?php if($_POST['age'] === '50代'){
                            echo 'selected';
                    };?>>50代</option> 
                </select>
            <p>メールアドレス：<input type="text" name="email" placeholder ="taroyamada@xxx.com" value="<?php  print(htmlspecialchars($_POST['email'],ENT_QUOTES));?>"/>
                <?php if($error['email'] === 'blank'):?>
                <p class="error">*メールアドレスを入力してください。</p>
                <?php endif;?>
                <?php if($error['email'] === 'duplicate'):?>
                <p class="error">*指定されたメールアドレスはすでに登録されています。</p>
                <?php endif;?>
            </p>
            <div class="checkbox01">学習中のプログラミング言語（複数選択可）：
            <label>
                  <!--check.phpで「戻る」ボタンを押下して遷移してきた時、セッションに登録されたvalue値各チェックボックスのcheckedの場合分けを行う-->
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="Java"
                <?php if($_REQUEST['action'] == 'rewrite'){
                        if(strpos($_SESSION['join']['p_language'],'Java') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">Java</span>
            </label>
            <label>
                  <!--check.phpで「戻る」ボタンを押下して遷移してきた時、セッションに登録されたvalue値各チェックボックスのcheckedの場合分けを行う-->
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="JavaScript"
                <?php if($_REQUEST['action'] == 'rewrite'){
                        if(strpos($_SESSION['join']['p_language'],'JavaScript') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">JavaScript</span>
            </label>
            <label>
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="PHP" 
                <?php if($_REQUEST['action'] == 'rewrite'){
                        if(strpos($_SESSION['join']['p_language'],'PHP') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">PHP</span>
            </label>
            <label>
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="Ruby" 
                <?php if($_REQUEST['action'] == 'rewrite'){
                        if(strpos($_SESSION['join']['p_language'],'Ruby') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">Ruby</span>
            </label>
            <label>
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="Python" 
                <?php if($_REQUEST['action'] == 'rewrite'){
                        if(strpos($_SESSION['join']['p_language'],'Python') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">Python</span>
            </label>
            <label>
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="HTML/CSS" 
                <?php if($_REQUEST['action'] == 'rewrite'){
                        if(strpos($_SESSION['join']['p_language'],'HTML/CSS') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">HTML/CSS</span>
            </label>
            <label>
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="Laravel" 
                <?php if($_REQUEST['action'] == 'rewrite'){
                        if(strpos($_SESSION['join']['p_language'],'Laravel') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">Laravel</span>
            </label>
            <label>
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="Ruby on Rails" 
                <?php if($_REQUEST['action'] == 'rewrite'){
                        if(strpos($_SESSION['join']['p_language'],'Ruby on Rails') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">Ruby on Rails</span>
            </label>
                <?php if($error['p_language'] === 'blank'):?>
                    <p class="error">*学習中のプログラミング言語を選択してください。</p>
                <?php endif;?>
            </div>
            <p>プロフィール画像：<input type="file" name="image" size="35" value="test"/></p>
            <?php if($error['image'] === 'type'):?>
                <p class="error">*写真などは「.gif」「.jpg」または「.png」の画像を指定してください。</p>
                <?php endif;?>
                <?php if(!empty($error)):?>
                <p class="error">*恐れ入りますが、画像をもう一度指定してください。</p>
                <?php endif;?>
            <p>パスワード：<input type="password" name="password" value="<?php print(htmlspecialchars($_POST['password'],ENT_QUOTES));?>"/>
                <?php if($error['password'] === 'length'):?>
                    <p class="error">*パスワードは4文字以上で入力してください。</p>
                <?php endif;?>
                <?php if($error['password'] === 'blank'):?>
                    <p class="error">*パスワードを入力してください。</p>
                <?php endif;?>
            </p>
            <input type="submit" value="確認">
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