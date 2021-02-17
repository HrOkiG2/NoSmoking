<?php
//sessionスタート
session_start();
require('../connect.php');

//formが送信されたときの処理
if($_POST){
    //formに入力された内容を確認
    if($_POST['name']===''){
        $error['name']='blank';
    }
    if($_POST['password']===''){
        $error['password']='blank';
    }
    if(strlen($_POST['password'])<4){
        $error['password']='length';
    }

    //パスワードが被っていないかの確認
    if(empty($error)){
        //$memberにmemberテーブルの全てのパスワードを格納
        $member=$db->prepare('SELECT COUNT(*) AS cnt FROM member WHERE password=?');

        $member->execute(array($_POST['password']));
        $record=$member->fetch();

        //$errorにduplicate格納
        if($record['password']>0){
            $error['password']='duplicate';
        }
    }


    //$errorが空ならfind.phpに遷移
    if(empty($error)){
        //formで入力した内容を$_sessionに格納
        $_SESSION['join']=$_POST;

        header('Location:find.php');
        exit();
    }

    //書き直す時に入力したデータを再現する
    if($_REQUEST['action']==='rewrite'){
        $_POST=$_SESSION['join'];
    }
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

        <h2>新規登録</h2>
        <form action="" method="POST">
            <!--名前の入力-->
            <p>お名前：</p>
            <input type="text" name="name" value="<?php print(htmlspecialchars($_POST['name'],ENT_QUOTES)); ?>">

            <!--name空文字のエラー文-->
            <?php if($error['name']==='blank'):?>
                <p class="error">名前を入力してください</p>
            <?php endif; ?>
                
            <!--パスワードの入力-->
            <p>パスワード：</p>
            <input type="password"　maxlength=10 name="password" value="<?php print(htmlspecialchars($_POST['password'],ENT_QUOTES));?>">

            <!--password空文字のエラー文-->
            <?php if($error['password']==='blank'): ?>
                <P class="error">パスワードを入力してください</P>
            <?php endif; ?>
            
            <!--password長さのエラー文-->
            <?php if($error['password']==='length'): ?>
                <P class="error">パスワードは４文字以上で入力してください</P>
            <?php endif; ?>
            

            <?php if($error['password']==='duplicate'): ?>
                <P class="error">このパスワードは既に使われています。</P>
            <?php endif; ?> 

            <br>
            
            <!--送信ボタン-->
            <input type="submit" value="入力内容を確認する">
       </form>
    </div>
</body>
</html>