<?php
session_start();
require('connect.php');

if($_COOKIE['name'] !==''){
    $name=$_COOKIE['name'];
}

//ログインしたときの処理
if($_POST){
    if($_POST['name'] !=='' && $_POST['password'] !==''){
        //名前が一致する人のパスワードを取得
        $users=$db->prepare('SELECT password FROM members WHERE name=:name');
        
        //バインド
        $users->bindValue(':name',$_POST['name'],PDO::PARAM_STR);

        //実行
        $users->execute();

        //取得
        $user_pass=$users->fetch();

        //取得したパスワードとハッシュしたパスワードの判定
        if(password_verify($_POST['password'],$user_pass['password'])){
                //パスワードが一致した時にmember_idとnameを取得する
                $members=$db->prepare('SELECT member_id,name FROM members WHERE name=:name');

                //バインド
                $members->bindValue(':name',$_POST['name'],PDO::PARAM_STR);

                //実行
                $members->execute();

                //取得
                $member=$members->fetch();

                //$_SESSION['id']にログイン者のmember_id,nameを格納
                $_SESSION['id']=$member;
                $_SESSION['time']=time();

                //cookie
                if($_POST['save']==='on'){
                    //3日間Cookieを保存
                    setcookie('name',$_POST['name'],time()+3600*24*3);
                }

                //一致していたらcheck.phpに遷移
                header('Location:check.php');
                exit();
        }else{
            $error['login']='blank';
        }
    }else{
        $error['login']='failed';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styl.css"/>
    <title>絶対!禁煙‼</title>
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
        <h2>禁煙で人生を大きく変えましょう</h2>
        <P>30日禁煙チャレンジ</P>

        <form action="" method="POST">
            <!--記入欄-->
            <P>お名前：</P>
            <input type="text" name="name" value="<?php print(htmlspecialchars($name,ENT_QUOTES)); ?>">

            <p>パスワード：</p>
            <input type="password" name="password" value="<?php print(htmlspecialchars($_POST['password'])); ?>">

            <!--エラー表示-->
            <?php if($error['login']==='blank'):?>
                <p class="error">正しい名前とパスワードを入力してください</p>
            <?php endif;?>
            
            <!--エラー表示-->
            <?php if($error['login']==='failed'):?>
                <p class="error">ログインに失敗しました</p>
            <?php endif;?>

            <br><br> 

            <input type="submit" value="ログイン">
            <br><br>
            <label>
                <input type="checkbox" name="save" value="on">
                次回からログインを簡略化する
            </label>
        </form>
    </div>
</body>
</html>