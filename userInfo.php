<?php
//sign.phpを元にしている
//予定変更につき未使用

require('head.php');
require('header.php');
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('アカウント情報変更ページを開きました');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
loginCheck();

//ユーザー情報取得
$userInfo = getUserInfo($_SESSION['user_id']);

//Ajaxで処理を行うためPHPのバリデーションは不要(あとで消す)

if(!empty($_POST)){
  debug('POST送信があります');
  $email = $_POST['email'];
  $pass =$_POST['pass'];
  $name = $_POST['name'];
  $pass_retype =$_POST['pass_retype'];

//emailのバリデーションに突入
  validEmail($email,'email');
  validMax($email,'email');
  validMinimum($email,'email');
  validEmpty($email,'email');
  validHalf($email,'email');
//登録済みemailアドレスのチェック
  validDuplication($email);
//passのバリデーションに突入  validEmail($pass,'pass');
  validMax($pass,'pass');
  validMinimum($pass,'pass');
  validEmpty($pass,'pass');
  validHalf($email,'email');
//nameのバリデーションに突入
  validMax($name,'name');
  validEmpty($name,'name');
//pass再入力との一致確認
  validMatch($pass,$pass_retype);



    if(empty($err_msg)){
      debug('全てのバリデーションを突破しました');
      try{
//DBに接続する
      $dbh = dbConnect();
      $sql = 'INSERT INTO users (email,pass,name,create_time,login_time) VALUES (:email,:pass,:name,:create_time,:login_time)';
      $data = array(':email' => $email,':pass' => password_hash($pass, PASSWORD_DEFAULT),':name' => $name,
                   ':create_time' => date('Y-m-d H:i:s'),
                   ':login_time' => date('Y-m-d H:i:s')
                   );
      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        $sesslimit = 60*60;
        $_SESSION['login_time'] = time();
        $_SESSION['login_limit'] = $sesslimit;
      }
      header('Location,mypage.php');
      }catch (Exeption $e){
      error_log('エラー：'.$e -> getMessage());
      $err_msg['common'] = msg06;
      }
    }else{
      debug('バリデーションに引っかかってます');
    }
}

?>
<div class="site-width">
  <section id = signup_form>
    <h1 id = signup_logo>アカウント情報変更</h1>
    <form action="" method = "post" class  = signup>
      <label>あなたのアカウント名
      <div class = err_msg_display ><?php if(!empty($err_msg['name'])) echo $err_msg['name'];?></div>
      <input name = "name" type="text" value = "<?php if(!empty($userInfo['name']))echo $userInfo['name'];?>"
      class = '<?php if(!empty($err_msg['name'])) echo "err";?>'>
      </label>
      
      <label for="">メールアドレス
      <div class = err_msg_display ><?php if(!empty($err_msg['email'])) echo $err_msg['email'];?></div>
      <input name = "email" type="text" value = "<?php if(!empty($userInfo['email']))echo $userInfo['email'];?>" 
      class = '<?php if(!empty($err_msg['email'])) echo "err";?>'>
      </label>
      
      <input type="submit" value ="送信">
    </form>
  </section>
</div>
<?php require('footer.php')?>