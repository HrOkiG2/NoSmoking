<?php 
//sessionで情報を持ってくる
session_start();
require('../connect.php');

//session['join']がセットされていなければnew_log.phpに戻る
if(!isset($_SESSION['join'])){
    header('Location:new_member.php');
    exit();
}

//formのPOSTが送信された時に$_POSTの情報をmembersテーブルに保存
if($_POST){
    //パスワードのハッシュ化
    $password_hush=password_hash($_SESSION['join']['password'],PASSWORD_DEFAULT);
    
    //membersテーブルに保存
    $statement=$db->prepare('INSERT INTO members(name,password,date) VALUES(:name,:password,now())');

    //バインド
    $statement->bindParam(':name',$_SESSION['join']['name'],PDO::PARAM_STR);
    $statement->bindParam(':password',$password_hush,PDO::PARAM_STR);
    
    //実行
    $statement->execute();

    //session['join']をクリアしてok.phpに遷移
    unset($_SESSION['join']);
    header('Location:ok.php');
    exit();

}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styl.css">
    <title>絶対!禁煙‼</title>
</head>
<body>
    <div class="all">
        <header>
            <h1>
               <img src="../image_folder/No_Smoking.png" alt="絶対！禁煙！！">
            </h1>
                <p class="use-to">
                    <a href="useto.php">使い方はこちら</a>
                </p>                     
        </header>

        <h2>登録情報はこちらで間違いないですか？</h2>
        <form action="" method="POST">
            <input type="hidden" name="action" value="submit">
            <div class="confilm">
                <!--名前の表示-->
                <p>名前:</p>
                <?php print(htmlspecialchars($_SESSION['join']['name'],ENT_QUOTES));?>

                <!--パスワードの表示-->
                <p>パスワード:</p>
                <?php print(htmlspecialchars($_SESSION['join']['password'],ENT_QUOTES));?>
            </div>

            <p>入力した内容に間違いがなければ登録するをクリックしてください</p>

            <div class="decision">
                <a href="new_member.php?action=rewrite">書き直す</a>
                <input type="submit" value="登録する">
            </div>
        </form>
    </div>
</body>
</html>