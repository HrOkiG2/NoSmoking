<?php
session_start();
require('connect.php');

if(isset($_SESSION['id']) && $_SESSION['time']+3600>time()){
    //$_SESSION['time']を現在の時刻に上書きする
    $_SESSION['time']=time();
    
    //mysqlの準備
    $mems=$db->prepare('SELECT * FROM members WHERE member_id=:member_id');
    
    //バインド
    $mems->bindParam(':member_id',$_SESSION['id']['member_id'],PDO::PARAM_STR);

    //実行
    $mems->execute();
    
    //取得
    $mem = $mems->fetch();
}else{
    //セットされていない時はlogin.phpを開く
    header('Location:login.php');
    exit();
}

//ログイン者の最新のデータ挿入日を取得
$datas=$db->prepare('SELECT MAX(created) FROM post WHERE member_id=:member_id ');
//バインド
$datas->bindValue(':member_id',$mem['member_id'],PDO::PARAM_INT);

//実行
$datas->execute();

//取得
$data=$datas->fetch(PDO::FETCH_COLUMN);

//ログイン者が本日のデータを挿入していたらmain.phpに遷移
if($data==date('Y-m-d')){
    header('Location:main.php');
}

//データの挿入
if($_POST['check']==='◯'){
    //ableのデータをpostテーブルに挿入
    $data=$db->prepare('INSERT INTO post(member_id,data,created) VALUES(:member_id,:able,now())');

    //バインド
    $data->bindParam(':member_id',$mem['member_id'],PDO::PARAM_STR);
    $data->bindParam(':able',$_POST['check'],PDO::PARAM_STR);
    
    //実行
    $data->execute();

    //$_post['check']を$session['judge']に格納
    $_SESSION['judge']=$_POST['check'];

    //main.phpに遷移
    header('Location:main.php');
    exit();
}elseif($_POST['check']==='✗'){
    //wrongのデータをpostsテーブルに挿入
    $data=$db->prepare('INSERT INTO post(member_id,data,created) VALUES(:member_id,:wrong,now())');

    //バインド
    $data->bindParam(':member_id',$mem['member_id'],PDO::PARAM_STR);
    $data->bindParam(':wrong',$_POST['check'],PDO::PARAM_STR);

    //実行
    $data->execute();

    //$_post['check']を$_session['judge']に格納
    $_SESSION['judge']=$_POST['check'];
    
    //main.phpに遷移
    header('Location:main.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="viewport" content="width=device-width,initial-scale=1.0">
        <title>絶対！禁煙！！</title>
        <link rel="stylesheet" href="styl.css">
    </head>
    <body>
        <div class="all">
            <header>
                <h1>
                    <img src="image_folder/No_Smoking.png" alt="絶対！禁煙！！">
                </h1>
                <p class="use-to">
                    <a href="useto.php">使い方はこちら</a>
                </p>
            </header>
            <h2>
                <?php print(htmlspecialchars($mem["name"],ENT_QUOTES)); ?>さんの禁煙チャレンジ
            </h2>
            <P>
                今日は我慢できましたか？
            </P>
            <!-- できたOR失敗を送信 -->
            <form method="POST" action=""> 
                <button input type="submit" name="check" value="◯">できた</button>
                <button input type="submit" name="check" value="✗">失敗した</button>                
            </form>
        </div>
    </body>
</html>