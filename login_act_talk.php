<?php
//最初にSESSIONを開始！！ココ大事！！
session_start();

//POST値
$lid=$_POST['lid'];
$lpw=$_POST['lpw'];

//1.  DB接続します
require_once('funcs_talk.php');
$pdo = db_conn();

//2. データ登録SQL作成  IDとPWが合っているユーザをDBから探す
$stmt = $pdo->prepare('SELECT * FROM gs_db_talkuser_table WHERE lid=:lid '); //SQLにはPW暗号化を解く事は不可なのでIDだけでの検索に切り替える
//$stmt = $pdo->prepare('SELECT * FROM gs_db_talkuser_table WHERE lid=:lid AND lpw=:lpw');  //PW暗号化前
$stmt->bindValue(':lid',$lid, PDO::PARAM_STR);
//$stmt->bindValue(':lpw',$lpw, PDO::PARAM_STR); //* Hash化する場合はコメントする
$status = $stmt->execute();

//3. SQL実行時にエラーがある場合STOP
if($status==false){
    sql_error($stmt);
}

//4. 抽出データ数を取得
$val = $stmt->fetch();           //1レコードだけ取得する方法
//$count = $stmt->fetchColumn(); //SELECT COUNT(*)で使用可能()

//5. 該当レコードがあればSESSIONに値を代入

if( password_verify($lpw, $val['lpw'])) { //PW暗号化後はこっち　PW突合作業
//if( $val['id']!="") {    //＝IDが存在したら。。。 PW暗号化前
  //Login成功時　　　米SESSION IDを持っている事が、これ以降「ログインしている」ことの確認・証明になる
  $_SESSION['chk_ssid']  = session_id();//SESSION変数にidを保存
  $_SESSION['kanri_flg'] = $val['kanri_flg'];//SESSION変数に管理者権限のflagを保存
  $_SESSION['name']      = $val['name'];//SESSION変数にnameを保存
  redirect('talk_trans.php');
}else{
  //Login失敗時(Logout経由)
  redirect('login_talk.php');
}

exit();


