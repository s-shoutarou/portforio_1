<?php
require('head.php');
require('header.php');
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('アカウント新規作成ページを開きました。');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
//loginCheck();

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
//  validHalf($email,'email');
//登録済みemailアドレスのチェック
  validDuplication($email);
//passのバリデーションに突入  validEmail($pass,'pass');
  validMax($pass,'pass');
  validMinimum($pass,'pass');
  validEmpty($pass,'pass');
//  validHalf($email,'email');
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
      debug(print_r($err_msg,true));
    }
}

?>
<div class="site-width">
  <section id = signup_form>
    <h1 id = signup_logo>アカウント作成</h1>
    <form action="" method = "post" class  = signup>
      <label>あなたのアカウント名
      <div class = err_msg_display ><?php if(!empty($err_msg['name'])) echo $err_msg['name'];?></div>
      <input name = "name" type="text" value = "<?php if(!empty($_POST['name']))echo $_POST['name'];?>"
      class = '<?php if(!empty($err_msg['name'])) echo "err";?>'>
      </label>
      
      <label for="">メールアドレス
      <div class = err_msg_display ><?php if(!empty($err_msg['email'])) echo $err_msg['email'];?></div>
      <input name = "email" type="text" value = "<?php if(!empty($_POST['email']))echo $_POST['email'];?>" 
      class = '<?php if(!empty($err_msg['email'])) echo "err";?>'>
      </label>
      
      <label for="">パスワード
      <div class = err_msg_display <?php if(!empty($err_msg['pass'])) echo "err";?>><?php if(!empty($err_msg['pass'])) echo $err_msg['pass'];?></div>
      <input type = 'password' name = "pass" type="text" value = "<?php if(!empty($_POST['pass']))echo $_POST['pass'];?>" class = '<?php if(!empty($err_msg['pass'])) echo "err";?>'>
      </label>
      
      <label for="">パスワード再入力
      <div class = err_msg_display <?php if(!empty($err_msg['pass_retype'])) echo "err";?>><?php if(!empty($err_msg['pass_retype'])) echo $err_msg['pass_retype'];?></div>
      <input type = 'password' name = "pass_retype" type="text" value = "<?php if(!empty($_POST['pass_retype']))echo $_POST['pass_retype'];?>" class = '<?php if(!empty($err_msg['pass_retype'])) echo "err";?>'>
      </label>
      
      <input type="submit" value ="送信">
    </form>
  </section>
</div>