

<?php

//SESSIONスタート
session_start();


require_once('funcs_talk.php');


//ログインチェック
loginCheck();

//以下ログインユーザーのみ
$user_name=$_SESSION['name'];
$kanri_flg=$_SESSION['kanri_flg'];  //０が一般　１が管理者　if文で画面を分けたりできる
$param_user_name_json = json_encode($user_name); //$user_nameをJSON変数に変換

//1.  DB接続します
$pdo=db_conn();

//２．SQL文を用意(データ取得：SELECT)
$stmt = $pdo->prepare("SELECT * FROM `gs_db_talk_table`");

//3. 実行
$status = $stmt->execute();

//4．データ表示
$view="";
if($status==false) {
    //execute（SQL実行時にエラーがある場合）
  $error = $stmt->errorInfo();
  exit("ErrorQuery:".$error[2]);

}else if($kanri_flg==1){
  //Selectデータの数だけ自動でループしてくれる
  //FETCH_ASSOC=http://php.net/manual/ja/pdostatement.fetch.php
  while( $result = $stmt->fetch(PDO::FETCH_ASSOC)){ 
    if($result['name']==$user_name){
        $view .="<div id='mymessagediv'>";
        $view .='<div id="myname">';
        $view .=($result['name']);
        $view .='</div>';
        $view .='<div id="mymsgdelete">';
        $view .='<div id="mymessage">';
        $view .=($result['message']);
        $view .='</div>';

        $view .='<a href="delete_talk.php?id='.$result['id'].'">';
        $view .='<button id="delete">';
        $view .='削除</button></a>';
        
        $view .='<button id="transl" class=" ';
        $view .=($result['message']);
        $view .='">翻訳</button>';

        $view .='</div>';

        $view .='<div id="mydate">';
        $view .=($result['indate']);
        $view .='</div></div>';
    }else{
        $view .="<div id='othermessagediv'>";
        $view .='<div id="othername">';
        $view .=($result['name']);
        $view .='</div>';
        $view .='<div id="othermsgdelete">';
        $view .='<div id="othermessage">';
        $view .=($result['message']);
        $view .='</div>';

        $view .= '<a href="delete_talk.php?id='.$result['id'].'">';
        $view .='<button id="delete">';
        $view .='削除</button></a>';

        $view .='<button id="transl" class=" ';
        $view .=($result['message']);
        $view .='">翻訳</button>';

        $view .='</div>';

        $view .='<div id="otherdate">';
        $view .=($result['indate']);
        $view .='</div></div>'; 
    }
    $param_json = json_encode($result); //JSONエンコード/JSの翻訳APIに入れる為にPHP変数をJSON変数に変換　今はWHILE処理の最後のデータしか変換できていない   
  }
}else{
    while( $result = $stmt->fetch(PDO::FETCH_ASSOC)){ 
    if($result['name']==$user_name){
        $view .="<div id='mymessagediv'>";
        $view .='<div id="myname">';
        $view .=($result['name']);
        $view .='</div>';
        $view .='<div id="mymsgdelete">';
        $view .='<div id="mymessage">';
        $view .=($result['message']);
        $view .='</div>';
    

            
        $view .='<button id="transl" class=" ';
        $view .=($result['message']);
        $view .='">翻訳</button>';
    
        $view .='</div>';
    
        $view .='<div id="mydate">';
        $view .=($result['indate']);
        $view .='</div></div>';
    }else{
        $view .="<div id='othermessagediv'>";
        $view .='<div id="othername">';
        $view .=($result['name']);
        $view .='</div>';
        $view .='<div id="othermsgdelete">';
        $view .='<div id="othermessage">';
        $view .=($result['message']);
        $view .='</div>';
    

    
        $view .='<button id="transl" class=" ';
        $view .=($result['message']);
        $view .='">翻訳</button>';
    
        $view .='</div>';
    
        $view .='<div id="otherdate">';
        $view .=($result['indate']);
        $view .='</div></div>'; 
    }
        $param_json = json_encode($result); //JSONエンコード/JSの翻訳APIに入れる為にPHP変数をJSON変数に変換　今はWHILE処理の最後のデータしか変換できていない   
    }



}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">  
<script src="https://cdn.webrtc.ecl.ntt.com/skyway-4.4.3.js"></script> 


<title>ラインアプリ（PHP&DB版）</title>
<link rel="stylesheet" href="reset.css">
<link rel="stylesheet" href="style.line.css">
</head>
<body>

<!-- コンテンツ表示画面 -->

<div class="head">ラインアプリ（PHP&DB版）</div>
<div>
    <div id="outputframe"><div id="output" ><?=$view?></div></div>
    <form method="post" action="insert_talk.php">
        <div id="inputarea">
            <div>
                <input type="text" id="uname" name="name" placeholder="name" readonly>
            </div>
            <div>
                <textarea  id="text" name="message" cols="30" rows="10"placeholder="message"></textarea>
                <button id="send">send</button>
            </div>
        </div>
    </form>
    <div id="idarea">
        <p>Your ID：　　</p>
        <p id="my-id"></p>
    </div>
    <br>
</div>
<div id="talkarea">
    <button id="talk">Video Mode</button>
    <button id="make-call">CALL</button>
</div>
<br>
<br>
<button id="allDelete">all delete</button>
<a class="navbar-brand" href="logout_talk.php">ログアウト</a>



<!-- JQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!-- JQuery -->
<script>
    $("#output").scrollTop(999999999);
    var param = JSON.parse('<?php echo $param_json; ?>'); //JSONデコード/翻訳APIのため、PHP変数をJSON変数として受け取る
    //console.log(param);


// 以下翻訳関連
    $(document).on("click","#transl", function () {
        const text=param.message;
        let apiKey = ''
        let fromLang = 'en'
        let toLang = 'ja'
        const URL = "https://translation.googleapis.com/language/translate/v2?key="+apiKey+
        "&q="+encodeURI(text)+"&source="+fromLang+"&target="+toLang
        let xhr = new XMLHttpRequest()
        xhr.open('POST', [URL], false)
        xhr.send();
            if (xhr.status === 200) {
                const res = JSON.parse(xhr.responseText);
                const textrev=res["data"]["translations"][0]["translatedText"];
                //console.log(textrev);
                
                //以下JSON変数をform形式を経由してPHPファイルに送るための処理
                // formタグを作成
                var tagForm = $('<form></form>');
                // action、method属性を指定。他は必要に応じて設定。
                tagForm.attr({
                    'action':   'update_talk.php',
                    'method':   'post',
                    'encoding': 'application/x-www-form-urlencoded',
                });
                
                // input要素たちをformタグに入れていく。
                let 
                h='<input type="text" name="name" value="';
                h+=param.name;
                h+='"><input type="text" name="message" value="';
                h+=textrev;
                h+='"><input type="hidden" name="id" value="';
                h+=param.id;
                h+='">';
                tagForm.append(h);

                // formタグを出力：出力しないと送信されないブラウザがいる
                tagForm.attr({
                   'style':    'overflow:hidden;width:0;height:0;'
                });
                $("body").append(tagForm);
 
                // 送信
                tagForm.submit();
            }
            
    });

</script>


<script type="module">

//ページ更新時の自分の名前プリセット
    var namae = JSON.parse('<?php echo $param_user_name_json; ?>'); //JSONデコード/PHP変数をJSON変数として受け取る
    $("#uname").val(namae);

  </script>


</body>
</html>
































