/*▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼
//DB 接続処理 ▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲*/ $dbhost="ホスト名";
$dbname="データベース名";
$dbuser="ユーザ名";
$dbpass="パスワード";
$dbtype="pgsql";
$dsn = "$dbtype:dbname=$dbname host=$dbhost port=5432";
try{
  $pdo=new PDO($dsn,$dbuser,$dbpass); 
  $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION); 
  $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
}catch(PDOException $Exception){ 
  die('エラー:'.$Exception->getMessage());
} /*▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲ ▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲▼▲*/

$Lname=$_POST["last_name"];
$Fname=$_POST["first_name"];
$work=$_POST["work"];
$Cid=$_POST["card_id"];
$sql_update="UPDATE sample_member SET last_name=?, first_name=?, work=? WHERE card_id= ?";
try{
  $stmh=$pdo->prepare($sql_update); 
  $stmh->execute(array($Lname,$Fname,$work,$Cid)); 
  print "更新しました。<br>";
}catch(PDOException $Exception){ 
  print "エラー";
}

/*■■■■■■■■■■■■■■■■■■■
(1)名前(作業名)、年、月、日
パターン番号=11
■■■■■■■■■■■■■■■■■■■■■*/
if($_POST["search_key"]!="" && $_POST["year"]!="" && $_POST["month"]!="" && $DAY!=""){

  $KEY1="11"; 
  $KEY21=$_POST["search_key"]; 
  $KEY31=$_POST["year"]; 
  $KEY32=$_POST["month"]; 
  $KEY33=$DAY;

  $tabname="b_".$_POST["year"]."_".$_POST["month"];//テーブル名作成 
  $tabsel="SELECT * FROM ".$tabname;//セレクト文作成 
  $search_key=$_POST["search_key"];
  //クエリ実行
  try{
  $stmh=$pdo->query($tabsel); 
  $stmh->execute(); 
  }catch(PDOException $Exception){
    print "エラー:"."データテーブルが見つかりません。<br>"; 
  }
  ?>
  <font size="3" color="#000000"><b>[指定内容]</b></font><br>
  <font size="3" color="#000000">名前 :</font>
  <font size="4" color="#ff0000"><?=$search_key?></font><br>
  <font size="3" color="#000000">作業内容 :</font>
  <font size="4" color="#ff0000"><?=$work?></font><br>
  <font size="3" color="#000000">指定年月日:</font>
  <font size="4" color="#ff0000"><?=$_POST["year"]?>年<?=$_POST["month"]?>月<?=$_POST["day"]?>日</font><br>

  <form name="formcsv" method="post" action="記録 CSV 処理.php"> 
    <input type="hidden" name="key1" value="<?=$KEY1?>"> 
    <input type="hidden" name="key21" value="<?=$KEY21?>"> 
    <input type="hidden" name="key31" value="<?=$KEY31?>"> 
    <input type="hidden" name="key32" value="<?=$KEY32?>"> 
    <input type="hidden" name="key33" value="<?=$KEY33?>"> 
    <input type="submit" value="CSV ファイルを保存">
  </form>

  <table width="1300" border="1" cellspacing="2" cellpadding="18">
  <tbody> 
  <tr><th>名前</th><th>作業時間[分]</th><th>作業内容</th><th>作業効率[%]</th><th>収穫ケース個数</th><th>レーン</th><th>年月日</th><th>時刻</th></tr> 

  <?php
  $rs = $stmh->fetchall ();

  foreach ( $rs as $row ) { if($work == ""){
  if(($row['member']==$search_key) && ($row['dd']==$DAY)){ 
  ?>
    <tr>
    <td align="center"><?=htmlspecialchars($row['member'])?></td> 
    <td align="center"><?=htmlspecialchars($row['work_time'])?></td> 
    <td align="center"><?=htmlspecialchars($row['work'])?></td> 
    <?php
    if($row['eff']>80 && $row['work']=="収穫"){
    ?>
      <td align="center" bgcolor="#7cfc00"><b><?=htmlspecialchars($row['eff'])?></b></td> 
    <?php
    }elseif($row['eff']>50 && $row['work']=="収穫"){ 
    ?>
      <td align="center" bgcolor="#00bfff"><b><?=htmlspecialchars($row['eff'])?></b></td> 
    <?php
    }elseif($row['eff']>30 && $row['work']=="収穫"){ 
    ?>
      <td align="center" bgcolor="#ffd700"><b><?=htmlspecialchars($row['eff'])?></b></td> 
    <?php
    }elseif($row['eff']<30 && $row['work']=="収穫" && $row['eff']!=""){ 
    ?>
      <td align="center" bgcolor="#ff4500"><b><?=htmlspecialchars($row['eff'])?></b></td> 
    <?php
    }else{ 
    ?>
      <td align="center"><?=htmlspecialchars($row['eff'])?></td> 
    <?php
    }
    ?>
    <td align="center"><?=htmlspecialchars($row['bx'])?></td>
    <td align="center"><?=htmlspecialchars($row['rane'])?></td> 
    <td align="center"><?=htmlspecialchars($row['d_ymd'])?></td> 
    <td align="center"><?=htmlspecialchars($row['dt'])?></td> 
    </tr>

<?php 
  }
}else{
  if(($row['member']==$search_key) && ($row['work']==$work) && ($row['dd']==$DAY)){ 
?>
    <tr>
    <td align="center"><?=htmlspecialchars($row['member'])?></td>
    <td align="center"><?=htmlspecialchars($row['work_time'])?></td> <td align="center"><?=htmlspecialchars($row['work'])?></td> 
    <?php
    if($row['eff']>80 && $row['work']=="収穫"){?>
    <td align="center" bgcolor="#7cfc00"><b><?=htmlspecialchars($row['eff'])?></b></td> 
    <?php
    }elseif($row['eff']>50 && $row['work']=="収穫"){ 
    ?>
      <td align="center" bgcolor="#00bfff"><b><?=htmlspecialchars($row['eff'])?></b></td> 
    <?php
    }elseif($row['eff']>30 && $row['work']=="収穫"){ 
    ?>
      <td align="center" bgcolor="#ffd700"><b><?=htmlspecialchars($row['eff'])?></b></td> 
    <?php
    }elseif($row['eff']<30 && $row['work']=="収穫" && $row['eff']!=""){ 
    ?>
      <td align="center" bgcolor="#ff4500"><b><?=htmlspecialchars($row['eff'])?></b></td> 
    <?php
    }else{ 
    ?>
      <td align="center"><?=htmlspecialchars($row['eff'])?></td> 
    <?php
    }
    ?>
    <td align="center"><?=htmlspecialchars($row['bx'])?></td>
    <td align="center"><?=htmlspecialchars($row['rane'])?></td> 
    <td align="center"><?=htmlspecialchars($row['d_ymd'])?></td> 
    <td align="center"><?=htmlspecialchars($row['dt'])?></td> 
    </tr>
  <?php
  } 
}
}//foreach の括弧 
if(count($rs) == 0){
  print "検索結果がありません。"; 
}
