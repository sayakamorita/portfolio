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

/*検索窓に何も入力されてなかったとき*/
if($_POST['searchThreadTitle'] === ''){
    $error['searchThreadTitle'] ='blank';
}
/*スレッドをたてた時に持っているhiddenでもっているflgを消去する*/
if(isset($_SESSION['create_thread']['flg'])){
    unset($_SESSION['create_thread']['flg']);
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

/*検索をするための処理*/
$search_word = $_POST['searchThreadTitle'];/*検索窓で入力された値*/
$search_word = '%'.$search_word.'%';
$search_sql='SELECT * FROM thread WHERE thread_id = "js" AND title like :search_word';
$search_stmt=$db->prepare($search_sql);
$search_stmt->bindParam(':search_word',$search_word,PDO::PARAM_STR);
$search_stmt->execute();

/*DBから、今何件スレッドが立っているか、数をとってくる*/
$count = $db->query('SELECT COUNT(*) as cnt FROM thread WHERE thread_id = "js"');
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
$threads = $db ->prepare('SELECT * FROM thread WHERE thread_id = "js" ORDER BY modified DESC LIMIT ?,10');
$threads->bindParam(1,$start,PDO::PARAM_INT);
$threads->execute();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>JavaScript質問スレッド一覧画面</title>
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
        <h2>JavaScript 初心者質問掲示板</h2>
        <!--スレッドタイトルの検索窓-->
        <form action="" name="search" method="post">
            <p><input type="text" size = "30" name="searchThreadTitle" placeholder="タイトルで検索"> 
            <input type="submit" class="button_link3" value="検索"></p>
        </form>
        <!--検索窓に値がいれられ、検索ボタンが押されたら検索結果を出力-->
       <?php 
        if(!empty($_POST) && $_POST['searchThreadTitle'] !== ''){
        echo '<table border ="1" class="thread_table">';
        echo '<tr>
                <th>タイトル</th>
                <th>投稿者</th>
                <th>更新日時</th>
                <th>詳細</th>
                <th>削除</th>
            </tr>';
        while($result = $search_stmt->fetch(PDO::FETCH_ASSOC)){
        print '<tr>';
        print '<td>'.htmlspecialchars($result['title'],ENT_QUOTES,'UTF-8').'</td>';
        print '<td>'.htmlspecialchars($result['member_name'],ENT_QUOTES,'UTF-8').'</td>';
        print '<td>'.htmlspecialchars($result['modified'],ENT_QUOTES,'UTF-8').'<br></td>';
        print '<td><a class="button_link" href="each_thread.php?thread_id='.htmlspecialchars($result['id']).'">詳細</a>'.'  '.'</td>';
        if($thread['member_name'] === $member['name']){
            print '<td><a class="button_link" href="delete_thread.php?thread_id='.htmlspecialchars($result['id']).'">削除</a>'.'  '.'</td>';}else{
            print '<td>削除</td>';
            }
            print '</tr>';
        }
        echo '</table>';
        print '<br><br>';
        print '<a class="button_link" href="thread_js.php">スレッド一覧に戻る</a>';
        
        }elseif($error['searchThreadTitle'] ==='blank'){
            echo '<p class="error">*タイトルを入力してください。';
        }
        ?>
            <?php 
            if(empty($_POST) || $_POST['searchThreadTitle'] ===''){
            echo '<h3>スレッド一覧('.htmlspecialchars($cnt['cnt']).'件)</h3>';
            echo '<br>';
            echo '<table border ="1" class="thread_table">';
            echo '<tr>
                    <th>タイトル</th>
                    <th>投稿者</th>
                    <th>更新日時</th>
                    <th>詳細</th>
                    <th>削除</th>
                  </tr>';
            foreach($threads as $thread){
                echo '<tr>';
                echo '<td>'.htmlspecialchars($thread['title']).'</td>';
                echo '<td>'.htmlspecialchars($thread['member_name']).'</td>';
                echo '<td>'.htmlspecialchars($thread['modified']).'</td>';
                /*詳細ボタンを、URLパラメーターを使ってforeach文の中にいれる。*/
            /* URLパラメータを使って、該当IDのeach_thread.phpに遷移する*/
                /*echo '<td><a href="?page_thread='.htmlspecialchars($thread['id']).'">詳細</a>'.'  '.'</td>';*/

                /*threadテーブルのidと、each_threadのthread_idをあわせる。*/
                echo '<td><a class="button_link" href="each_thread.php?thread_id='.htmlspecialchars($thread['id']).'">詳細</a>'.'  '.'</td>';
                if($thread['member_name'] === $member['name']){
                    echo '<td><a class="button_link" href="delete_thread.php?thread_id='.htmlspecialchars($thread['id']).'">削除</a>'.'  '.'</td>';
                }else{
                    echo '<td>削除</td>';
                    }
                echo '</tr>';
            }
            echo '</table>';
            };?>
            
            <?php if(empty($_POST) || $_POST['searchThreadTitle'] ===''):?>
                <br>
                <div>
                    <ul class="paging">
                        <?php if($page >1):?>
                            <li><a class="button_link" href="thread_js.php?page_thread=<?php print(htmlspecialchars($page -1));?>">前のページへ</a></li>
                        <?php else:?>
                            <li>前のページへ</li>
                        <?php endif;?>
                        <?php if($page < $maxPage):?>
                            <li><a class="button_link" href="thread_js.php?page_thread=<?php print(htmlspecialchars($page+1));?>">次のページへ</a></li>
                        <?php else:?>
                            <li>次のページへ</li>    
                        <?php endif;?>
                    </ul>
                    <?php endif;?>
                </div>
                <br>
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
