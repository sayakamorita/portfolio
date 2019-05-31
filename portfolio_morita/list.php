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
                <li><a class="header_link" href="admin_logout.php">ログアウト</a></li>
            </ul>
        </div>
    </div>
    <!--ヘッダー終了-->
    <!--コンテンツ開始-->
    <div id="top_content">
        <h2>登録者情報一覧</h2>
        <form action="" name="search" method="post">
            <div><input type="text" name="searchUserName" placeholder="山田太郎"></div> 
            <div><input type="submit" class="button_list2" value="検索"></div>
        </form>
        <!--検索窓に値がいれられ、検索ボタンが押されたら検索結果を出力-->
       <?php 
        if(!empty($_POST) && $_POST['searchUserName'] !== ''){
        while($result = $search_stmt->fetch(PDO::FETCH_ASSOC)){
        print '<p>'.htmlspecialchars($result['id'],ENT_QUOTES,'UTF-8').'</p>';
        print '<p>'.htmlspecialchars($result['name'],ENT_QUOTES,'UTF-8').'</p>';
        print '<p>'.htmlspecialchars($result['email'],ENT_QUOTES,'UTF-8').'<br></p>';
        }}
        ?>

        <!--tableで全ての情報一覧を取得する-->
        <table style="margin : 0 auto">
            <tr>
                <td>登録者ID</td>
                <td>ニックネーム</td>
                <td>メールアドレス</td>
            </tr>
        <?php 
        /*検索ボタンが押されていなかったら、リスト一覧を表示する*/
        if(empty($_POST) || $_POST['searchUserName'] ===''){
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
        }?>
        </table>
        <div>
            <ul class="paging">
                <?php if($page >1):?>
                    <li><a href="list.php?page_list=<?php print(htmlspecialchars($page -1));?>">前のページへ</a></li>
                <?php else:?>
                    <li>前のページへ</li>
                <?php endif;?>
                <?php if($page < $maxPage):?>
                    <li><a href="list.php?page_list=<?php print(htmlspecialchars($page+1));?>">次のページへ</a></li>
                <?php else:?>
                    <li>次のページへ</li>    
                <?php endif;?>
            </ul>
            <p><a class="button_link2" href="admin_logout.php">ログアウト</a></p>
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