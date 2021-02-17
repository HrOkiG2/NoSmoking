<?php
session_start();
require('connect.php');

//カレンダー
    //現在時刻の設定
    date_default_timezone_get('Asia/Tokyo');

    //年と月を設定する
    $year=date('Y');
    $month=date('m');

    //月末日を取得
    $end_month=date('t',strtotime($year.$month.'01'));

    //月初めの曜日を取得
    $first_week=date('w',strtotime($year.$month.'01'));

    //月末の曜日を取得
    $last_week=date('w',strtotime($year.$month.$end_month));

    $calender=[];
    $j=0;

    //1日が始まるまでの穴埋め
    for($i=0; $i<$first_week; $i++){
        $calender[$j][]='';
    }

    //1日から月末までループ
    for($i=1;$i<=$end_month;$i++){
        //日曜日まで進んだら改行
        if(isset($calender[$j]) && count($calender[$j]) === 7){
            $j++;
        }
        $calender[$j][]=$i;
    }

    //月末曜日までの穴埋め
    for($i = count($calender[$j]);$i<7;$i++){
        $calender[$j][]='';
    }


    $aryweek=['日','月','火','水','木','金','土'];

    //ログイン情報を呼び出す
    $mems=$db->prepare('SELECT * FROM members WHERE member_id=:member_id');

    //バインド
    $mems->bindParam(':member_id',$_SESSION['id']['member_id'],PDO::PARAM_STR);

    //実行
    $mems->execute();
    $mem=$mems->fetch();

    //ログイン者のデータカラムを取り出す
    $datas=$db->prepare('SELECT data,created FROM post WHERE member_id=:member_id AND created between date(:gessyo) and date(:getumatu)');

    //今月のデータを取得するために用意
    $new_day=date('Y-m-01');
    $end_day=date('Y-m-t');

    //バインド
    $datas->bindValue(':member_id',$mem['member_id'],PDO::PARAM_STR);
    $datas->bindValue(':gessyo',$new_day,PDO::PARAM_STR);
    $datas->bindValue(':getumatu',$end_day,PDO::PARAM_STR);
    
    //実行
    $datas->execute();

    //取得
    $data=$datas->fetchAll(PDO::FETCH_NAMED);

    //写真と吹き出しの表示
    if($_SESSION['judge']){
        //できたボタンが押されたとき
        if($_SESSION['judge']==='◯'){
            $image="image_folder/smile.png";

            //吹き出しのコメント
            $compliment=[
                "流石です!!!",
                "今日も一歩前進です",
                "この調子でいきましょう",
                "歯が白くなってきてますよ",
                "今日もタバコから旅立てた！"
            ];
        }elseif($_SESSION['judge']==='✗'){
            //失敗したボタンが押されたとき
            $image="image_folder/oni.png";

            //吹き出しのコメント
            $defeat=[
                "口が臭いのにタバコを吸い続けるの？",
                "あなたの意思はゴミです",
                "タバコ辞めるか人間辞めるかの二択です",
                "今日で最後の一本にしてください",
                "ばーーーか"
            ];
        }
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>絶対！禁煙！！</title>
    <link rel="stylesheet" href="styl.css">
</head>
<body>
    <header>
        <h1>
            <img src="image_folder/No_Smoking.png" alt="絶対！禁煙！！">
        </h1>
        <p class="use-to">
            <a href="useto.php">使い方はこちら</a>
        </p>
    </header>

    <h2><?php print(htmlspecialchars($mem['name'],ENT_QUOTES));?>さんの禁煙チャレンジ</h2>
    
    <!--写真の表示-->
    <div class="situation">
        <img src="<?php echo $image ;?>">
    
        <!--吹き出しの表示-->
        <div class="balloon1-left">
            <p>
                <?php if($_SESSION['judge']){

                    //できたボタンが押されたとき
                    if($_SESSION['judge']==='◯'){
                        //配列の中の要素にランダムに番号をつける
                        $randm=array_rand($compliment,1);
                        
                        //インデックス番号に$randmを指定
                        echo $compliment[$randm];

                    }//失敗したボタンが押されたとき
                    elseif($_SESSION['judge']==='✗'){
                        //配列の中の要素にランダムに番号をつける
                        $randm=array_rand($defeat,1);

                        //インデックス番号に$randmを指定
                        echo $defeat[$randm];
                    }
                }?>
            </p>
        </div>
    </div>

    <h3>
        <!--カレンダータイトル-->
        <?php echo $year.'年'.$month.'月のカレンダー' ;?>
    </h3>
    <table class="calender" border="5">
        <tr>
            <?php foreach($aryweek as $week){;?>
                <th>
                    <?php echo $week;?>    
                </th>
            <?php };?>
        </tr>
        <!-- カレンダーのマスを成形する-->
        <?php foreach($calender as $tr){;?>
            <tr>
            <?php foreach($tr as $td){;?>
                    <?php
                    if($td !=date('j')){
                        echo"<td>";
                            echo $td.'<br>';
                            //カレンダー出力のため<div class="data">指定
                            echo "<div class='data'>";
                                for($i=0;$i<=$end_month;$i++){
                                    if($td<10){
                                        //日付と比較するために$tdに０を結合
                                        $contrast='0'.$td;

                                        if($data[$i]['created']==date('Y-m').'-'.$contrast){
                                            //$tdが10未満だったときの出力
                                            echo $data[$i]['data'];
                                        }

                                    }elseif($data[$i]['created']==date('Y-m').'-'.$td){
                                        //$tdが10以上だったときの出力
                                        echo $data[$i]['data'];
                                    }
                                }
                            echo "</div>";
                        echo "</td>";
                    }else{
                        //今日のデータ
                        echo "<td class='today_color'>";
                            echo $td."<br>"."<div class=data>".$_SESSION['judge']."</div>";
                        echo "</td><br>";
                    }
                    ?>
            <?php };?>
            </tr>
        <?php };?>  
    </table>
</body>
</html>