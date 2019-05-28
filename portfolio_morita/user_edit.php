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

/*ログインしている場合の処理*/
if(isset($_SESSION['id'])){
$datas = $db->prepare('SELECT * FROM members WHERE id = ?');
/*URLパラメータのIDに指定されている数字を取り出す
list.phpの編集ボタンのURLパラメーターpage_listでおくられてきた数字を、上のSQL文のid＝？に代入して値を取り出す*/
$datas ->execute(array($_REQUEST['id']));
$data = $datas->fetch();
/*↑この時点で$dataにはDBに登録されている値が格納されている*/}

/*各入力項目のエラーを定義する*/
if(!empty($_POST)){
    /*formのname属性(name)が入力されていなかったときの処理*/
    if($_POST['name'] === ''){
        /*$errorという配列のnameの中に、blankという何も入力されていないエラー状況を格納
          これを条件として、入力されていなかった時のエラーチェックをif文で行うことができる。*/
        $error['name'] = 'blank';
    }

    if($_POST['email'] === ''){
        $error['email'] = 'blank';
    }

    /*パスワードが4文字以下の場合のエラーチェック*/
    if(strlen($_POST['password']) < 4){
        $error['password'] = 'length';
    }
    if($_POST['password'] === ''){
        $error['password'] = 'blank';
    }
    
    if(empty($_POST['p_language'])){
        $error['p_language'] = 'blank';
        }
    }    

    /*すべてのエラーチェックが完了し、エラーがない状態であればuser_edit_check.phpに遷移するようにする*/
    if(!empty($_POST)){
        if(empty($error)){
            /*エラーチェックが完了したあとに、editという名前のセッションに$_POST（formで送信されてきた値）を格納する処理*/
            $_SESSION['user_edit'] = $_POST;
            /*プログラミング言語が選択されている場合、「,」で区切って確認画面で表示させる*/
            if(isset($_POST['p_language'])){
                $p_language = implode(" , ",$_POST['p_language']);
            }
            $_SESSION['user_edit']['p_language'] = $p_language;
            $_SESSION['user_edit']['id'] = $data['id'];
            header('Location:user_edit_check.php');
            exit();
        }
    }
/*urlパラメーターにaction=rewrite_editと表示されていたら、フォームにセッションに保存された値（パスワード以外）を出力する→edit_check.phpで「戻る」ボタンを押下して遷移してきた時の処理。*/
if($_REQUEST['user_rewrite'] == 'rewrite_edit'){
    $_POST['name'] = $_SESSION['user_edit']['name'];
    $_POST['email'] = $_SESSION['user_edit']['email'];
    $_POST['age'] = $_SESSION['user_edit']['age'];
    $_POST['sex'] = $_SESSION['user_edit']['sex'];
    $_POST['p_language'] = $_SESSION['user_edit']['p_language'];
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>編集画面</title>
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
            <li><a class="header_link" href="user_detail.php">ユーザー登録情報</a></li>
            <li><a class="header_link" href="logout_p.php">ログアウト</a></li>
            </ul>
        </div>
    </div>
    <!--ヘッダー終了-->
    <!--コンテンツ開始-->
    <div id="content">
    <h2>ユーザー登録情報編集画面</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <!-- value属性には、$data['name'](編集前の登録データ）を設定して出力する-->
        <!--URLがedit=rewrite_editだった場合、valueには編集したあとの値を出力する(if文で場合分け)-->
            <p>ニックネーム：<input type="text" name="name" 
            <?php if($_REQUEST['user_rewrite'] === 'rewrite_edit'):?>
            value="<?php print(htmlspecialchars($_SESSION['user_edit']['name'],ENT_QUOTES));?>"/>
            <?php else:?>
            value="<?php print(htmlspecialchars($data['name'],ENT_QUOTES));?>"
                <?php endif;?>
                <?php if($error['name'] === 'blank'):?>
                <p class="error">*ニックネームを入力してください。</p>
                <?php endif;?>
            </p>   
            <!--check.phpで「戻る」ボタンを押下して遷移してきた時、セッションに登録されたvalue値でラジオボタンcheckedの場合分けを行う-->
            <div class="radio">性別：
            <!--edit_check画面で戻るボタンをおされているかつセッションeditが存在しなかったら、-->
                <input type="radio" value="男性" name="sex" id="man"  
                <?php if($_REQUEST['user_rewrite'] === 'rewrite_edit'&& $_POST['sex'] === '男性'){
                    echo 'checked';
                }elseif($data['sex'] === '男性'){
                    echo 'checked';
                };?>>
                <label for="radio02-01">男性</label>

                <input type="radio" value="女性" name="sex" id="woman"  
                <?php if($_REQUEST['user_rewrite'] === 'rewrite_edit' && $_POST['sex'] === '女性') {
                    echo 'checked';
                }elseif($data['sex'] === '女性'){
                    echo 'checked';
                };?>>
                <label for="radio02-01">女性</label>

                <input type="radio" value="選択しない" name="sex" id="other"  
                <?php if($_REQUEST['user_rewrite'] === 'rewrite_edit' && $_POST['sex'] === '選択しない'){
                    echo 'checked';
                }elseif($data['sex'] === '選択しない'){
                    echo 'checked';
                };?>>
                <label for="radio02-01">選択しない</label>
            </div>

            <p>年齢：</p>
                <select name='age'>
                <option value='' disabled style='display:none;'>選択してください</option>
                    <option value='10代' 
                    <?php if($_REQUEST['user_rewrite'] === 'rewrite_edit' && $_POST['age'] === '10代'){
                    echo 'selected';
                }elseif($data['age'] === '10代'){
                    echo 'selected';
                };?>>
                    
                    10代</option>
                    <option value='20代' 
                    <?php if($_REQUEST['user_rewrite'] === 'rewrite_edit' && $_POST['age'] === '20代'){
                    echo 'selected';
                }elseif($data['age'] === '20代'){
                    echo 'selected';
                };?>>
                    
                    20代</option>

                    <option value='30代' 
                    <?php if($_REQUEST['user_rewrite'] === 'rewrite_edit' && $_POST['age'] === '30代'){
                    echo 'selected';
                }elseif($data['age'] === '30代'){
                    echo 'selected';
                };?>>
                    
                    30代</option>

                    <option value='40代' 
                    <?php if($_REQUEST['user_rewrite'] === 'rewrite_edit' && $_POST['age'] === '40代'){
                    echo 'selected';
                }elseif($data['age'] === '40代'){
                    echo 'selected';
                };?>>
                    
                    40代</option>

                    <option value='50代' 
                    <?php if($_REQUEST['user_rewrite'] === 'rewrite_edit' && $_POST['age'] === '50代'){
                    echo 'selected';
                }elseif($data['age'] === '50代'){
                    echo 'selected';
                };?>>
                    
                    50代</option>
                     
                </select>
                <p>メールアドレス：<input type="text" name="email" 
            <?php if($_REQUEST['user_rewrite'] === 'rewrite_edit'):?>
            value="<?php print(htmlspecialchars($_POST['email'],ENT_QUOTES));?>">
            <?php else:?>
            value="<?php print(htmlspecialchars($data['email'],ENT_QUOTES));?>"
            <?php endif;?>
                <?php if($error['email'] === 'blank'):?>
                <p class="error">*メールアドレスを入力してください。</p>
                <?php endif;?>
            </p>   
                <?php if($error['email'] === 'duplicate'):?>
                <p class="error">*指定されたメールアドレスはすでに登録されています。</p>
                <?php endif;?>
            </p>
            <div class="checkbox01">学習中のプログラミング言語（複数選択可）：
            <label>
                  <!--edit_check.phpで「戻る」ボタンを押下して遷移してきた時、セッションに登録されたvalue値各チェックボックスのcheckedの場合分けを行う-->
                  <!--もともと登録されていたデータにJavaが入っていたら、それもchecked。-->
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="Java"
                <?php if(strpos($data['p_language'],'Java') !== false){
                            echo 'checked';
                    }?>
                <?php if($_REQUEST['user_rewrite'] == 'rewrite_edit'){
                        if(strpos($_SESSION['user_edit']['p_language'],'Java') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">Java</span>
            </label>
            <label>
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="JavaScript" 
                <?php if(strpos($data['p_language'],'JavaScript') !== false){
                            echo 'checked';
                    }?>
                <?php if($_REQUEST['user_rewrite'] == 'rewrite_edit'){
                        if(strpos($_SESSION['user_edit']['p_language'],'JavaScript') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">JavaScript</span>
            </label>
            <label>
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="PHP" 
                <?php if(strpos($data['p_language'],'PHP') !== false){
                            echo 'checked';
                    }?>
                <?php if($_REQUEST['user_rewrite'] == 'rewrite_edit'){
                        if(strpos($_SESSION['user_edit']['p_language'],'PHP') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">PHP</span>
            </label>
            <label>
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="Ruby" 
                <?php if(strpos($data['p_language'],'Ruby') !== false){
                            echo 'checked';
                    }?>
                <?php if($_REQUEST['user_rewrite'] == 'rewrite_edit'){
                        if(strpos($_SESSION['user_edit']['p_language'],'Ruby') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">Ruby</span>
            </label>
            <label>
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="Python" 
                <?php if(strpos($data['p_language'],'Python') !== false){
                            echo 'checked';
                    }?>
                <?php if($_REQUEST['user_rewrite'] == 'rewrite_edit'){
                        if(strpos($_SESSION['user_edit']['p_language'],'Python') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">Python</span>
            </label>
            <label>
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="HTML/CSS" 
                <?php if(strpos($data['p_language'],'HTML/CSS') !== false){
                            echo 'checked';
                    }?>
                <?php if($_REQUEST['user_rewrite'] == 'rewrite_edit'){
                        if(strpos($_SESSION['user_edit']['p_language'],'HTML/CSS') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">HTML/CSS</span>
            </label>
            <label>
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="Laravel" 
                <?php if(strpos($data['p_language'],'Laravel') !== false){
                            echo 'checked';
                    }?>
                <?php if($_REQUEST['user_rewrite'] == 'rewrite_edit'){
                        if(strpos($_SESSION['user_edit']['p_language'],'Laravel') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">Laravel</span>
            </label>
            <label>
                <input type="checkbox" name="p_language[]" class="checkbox01-input" value="Ruby on Rails" 
                <?php if(strpos($data['p_language'],'Ruby on Rails') !== false){
                            echo 'checked';
                    }?>
                <?php if($_REQUEST['user_rewrite'] == 'rewrite_edit'){
                        if(strpos($_SESSION['user_edit']['p_language'],'Ruby on Rails') !== false){
                            echo 'checked';
                        }
                    }?>>
                <span class="checkbox01-parts">Ruby on Rails</span>
            </label>
                <?php if($error['p_language'] === 'blank'):?>
                    <p class="error">*学習中のプログラミング言語を選択してください。</p>
                <?php endif;?>
                </div>
            <p>新しいパスワード：<input type="password" name="password" value="">
            <?php if($error['password'] === 'length'):?>
                    <p class="error">*パスワードは4文字以上で入力してください。</p>
                <?php endif;?>
                <?php if($error['password'] === 'blank'):?>
                    <p class="error">*パスワードを入力してください。</p>
                <?php endif;?>
            <p>新しいパスワード(確認)：<input type="password" name="password_confirm" value="">
            <?php if($_POST['password'] !== $_POST['password_confirm']):?>
            <p class="error">正しいパスワードを入力してください。</p>
                <?php endif;?>
            <p>登録者情報に戻る場合は<a href="user_detail.php">こちら</a></p>
            <input type="submit" value="確認">
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