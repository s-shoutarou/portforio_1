<?php
$site_title = 'ログイン';

require('function.php');
require('header.php');
require('head.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ログインページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

loginCheck();


if(!empty($_POST)){
  debug('POST送信があります');
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true:false;
  
  $postCheck = $_POST;
  debug(print_r($postCheck,true));

//emailチェック//////////////////////////////////////////////////////////////////////////////////
  validEmail($email,'email');

  validMax($email,'email');

  validMinimum($email,'email');

  validEmpty($email,'email');

//  validHalf($email,'email');

//passチェック//////////////////////////////////////////////////////////////////////////////////
  validMax($pass,'pass');

  validMinimum($pass,'pass');

  validEmpty($pass,'pass');

  validHalf($pass,'pass');
//DB接続//////////////////////////////////////////////////////////////////////////////////

  if(empty($err_msg)){
    debug('全てのバリデーションを突破しました');
    debug('ログインを開始します');
//例外処理
      try{
        $dbh = dbConnect();
        $sql = 'SELECT pass,id FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email'=>$email);
        $stmt = queryPost($dbh,$sql,$data);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!empty($result)){
            debug('クエリの結果の表示：'.print_r($result,true));
        }else{
          debug('クエリによってデータを取得して居ません');
        }

  
          if(!empty($result) && password_verify($pass,array_shift($result))){
              debug('パスワードがマッチしました');
              $sesLimit = 60*60;
              $_SESSION['login_time'] = time();
              $_SESSION['login_limit'] = $sessLimit;
    
                if($pass_save){
                  debug('ログイン情報保持にチェックがあります');
                  $_SESSION['login_limit'] = $sesLimit * 24 * 30;
                }else{
                  debug('ログイン保持にチェックがありません');
                  $_SESSION['login_limit'] = $sesLimit;
                }
    
            $_SESSION['user_id'] = $result['id'];
            debug('セッション変数の中身：'.print_r($_SESSION,true));
            debug('マイページへ遷移します');
            header("Location:mypage.php");
          }else{
            debug('パスワードが不一致です');
            $err_msg['common'] = msg05;
          }
      } catch(Exeption $e){
        debug('ログインに失敗しました');
        error_log("エラー：".$e -> getMessage());
        $err_msg['common'] = msg06;
      }
  }
}

?>

<div class="site-width">
<form action = "" method = post id = login_form>
<section id = login>
 <h1 id = login_logo>ログイン</h1>
  <label for="">
    メールアドレス
     <div class = err_msg_display <?php if(!empty($err_msg['email'])) echo "err";?>><?php if(!empty($err_msg['email'])) echo $err_msg['email'];?></div>
     <input type="text" name = "email" value = "<?php if(!empty($_POST['email'])) echo $_POST['email'];?>">
  </label>
  <label for="">
     パスワード
     <div class = err_msg_display <?php if(!empty($err_msg['pass'])) echo "err";?>><?php if(!empty($err_msg['pass'])) echo $err_msg['pass'];?></div>
     <input type="password" name = "pass" value = "<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>">
  </label>
   <label>
      <input type="checkbox" name ='pass_save' >次回から自動ログインする
  </label>
    <input type="submit" value = "ログイン" class = login_btn>
  </form>
</section>
</div>


<?php
  require('footer.php');
  ?>