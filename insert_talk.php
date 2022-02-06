<?php
// 1. POSTデータ取得
//$name = filter_input( INPUT_GET, ","name" ); //こういうのもあるよ
//$email = filter_input( INPUT_POST, "email" ); //こういうのもあるよ
$name=$_POST["name"];
$message=$_POST["message"];


// 2. DB接続します
require_once('funcs_talk.php');
$pdo=db_conn();


// ３．SQL文を用意(データ登録：INSERT)
$stmt = $pdo->prepare(
  "INSERT INTO `gs_db_talk_table`(`id`, `name`, `message`, `indate`) 
  VALUES (NULL,:name,:message,sysdate())"
);

// 4. バインド変数を用意
$stmt->bindValue(':name', $name, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':message', $message, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)

// 5. 実行
$status = $stmt->execute();

// 6．データ登録処理後
if($status==false){
  sql_error($stmt);
}else{
  redirect('talk_trans.php');
}
?>
