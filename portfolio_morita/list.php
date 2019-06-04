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

/*DBから全てのデータをとってくる
$sql = $db->query('SELECT * FROM members');*/


/*検索をするための処理*/
$search_word = $_POST['searchUserName'];/*検索窓で入力された値*/
$search_word = '%'.$search_word.'%';
$search_sql='SELECT * FROM members WHERE name like :search_word';
$search_stmt=$db->prepare($search_sql);
$search_stmt->bindParam(':search_word',$search_word,PDO::PARAM_STR);
$search_stmt->execute();

/*今何件memberテーブルにデータが登録されているか*/
$count = $db->query('SELECT COUNT(*) as cnt FROM members');
$cnt = $count->fetch();

/*ページネーションの処理*/
$page = $_REQUEST['page_list'];
if($page == ''){
    $page = 1;
}
$page = max($page,1);
$maxPage = ceil($cnt['cnt'] /10);
$page = min($page,$maxPage);

$start = ($page -1)*10;
/*DBから投稿を取得する*/
$members = $db ->prepare('SELECT * FROM members ORDER BY created LIMIT ?,10');
$members->bindParam(1,$start,PDO::PARAM_INT);
$members->execute();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>登録者情報一覧画面</title>
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
            <li class="nav-item"><a class="nav-link" href="admin_logout.php">ログアウト</a></li>
        </ul>
        </div>
    </nav>
<!--ヘッダー終了-->
    <!--コンテンツ開始-->
    <div id="content">
        <h2>登録者情報一覧</h2>
        <br>
        <form action="" name="search" method="post">
            <p>ユーザー情報検索：<input type="text" name="searchUserName" placeholder="山田太郎">
            <input type="submit" class="button_list2" value="検索"></p>
        </form>
        <br>
        <hr>
        <!--検索窓に値がいれられ、検索ボタンが押されたら検索結果を出力-->
       <?php 
        if(!empty($_POST) && $_POST['searchUserName'] !== ''){
        echo '<h3>検索結果一覧</h3>';
        echo '<table border ="1" class="thread_table">';
        echo '<tr>
                <th>ユーザーID</th>
                <th>ニックネーム</th>
                <th>メールアドレス</th>
                <th>詳細</th>
                <th>削除</th>
            </tr>';
        while($result = $search_stmt->fetch(PDO::FETCH_ASSOC)){
        print '<tr>';
        print '<td>'.htmlspecialchars($result['id'],ENT_QUOTES,'UTF-8').'</td>';
        print '<td>'.htmlspecialchars($result['name'],ENT_QUOTES,'UTF-8').'</td>';
        print '<td>'.htmlspecialchars($result['email'],ENT_QUOTES,'UTF-8').'<br></td>';
        print '<td><a class="button_link" href="detail.php?page_list='.htmlspecialchars($result['id']).'">詳細</a>'.'  '.'</td>';
        print '<td><a class="button_link" href="list_delete.php?delete_list='.htmlspecialchars($result['id']).'">削除</a>'.'  '.'</td>';
        print '</tr>';
        }
        echo '</table>';
        print '<br><br>';
        print '<a class="button_link" href="list.php">スレッド一覧に戻る</a>';
        }
        ?>

        <?php 
        /*検索ボタンが押されていなかったら、リスト一覧を表示する*/
        if(empty($_POST) || $_POST['searchUserName'] ===''){
            echo '<h3>ユーザー情報一覧('.htmlspecialchars($cnt['cnt']).'件)</h3>';
            echo '<table border ="1" class="thread_table">';
            echo '<tr>
                    <th>ユーザーID</th>
                    <th>ニックネーム</th>
                    <th>メールアドレス</th>
                    <th>詳細</th>
                    <th>削除</th>
                  </tr>';
            foreach($members as $member){
            echo '<tr>';
            echo '<td>'.htmlspecialchars($member['id']).' '.'</td>';
            echo '<td>'.htmlspecialchars($member['name']).' '.'</td>';
            echo '<td>'.htmlspecialchars($member['email']).' '.'</td>';
            /*編集ボタンも、URLパラメーターを使ってforeach文の中にいれる。*/
           /* URLパラメータを使って、該当IDのedit.phpに遷移する*/
            /*詳細、削除ボタンも、URLパラメーターを使ってこの中にいれこむ*/
            echo '<td><a class="button_link" href="detail.php?page_list='.htmlspecialchars($member['id']).'">詳細</a>'.'  '.'</td>';
            echo '<td><a class="button_link" href="list_delete.php?delete_list='.htmlspecialchars($member['id']).'">削除</a></td>';
            echo '</tr>';
            }
            echo '</table>';
        }?>

        <?php if(empty($_POST) || $_POST['searchUserName'] ===''):?>
        <br>
        <div>
            <ul class="paging">
                <?php if($page >1):?>
                    <li><a class="button_link" href="list.php?page_list=<?php print(htmlspecialchars($page -1));?>">前のページへ</a></li>
                <?php else:?>
                    <li>前のページへ</li>
                <?php endif;?>
                <?php if($page < $maxPage):?>
                    <li><a class="button_link" href="list.php?page_list=<?php print(htmlspecialchars($page+1));?>">次のページへ</a></li>
                <?php else:?>
                    <li>次のページへ</li>    
                <?php endif;?>
            </ul>
        <?php endif;?>
        <br>
        <p><a class="button_link" href="admin_logout.php">ログアウト</a></p>
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