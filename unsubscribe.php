<?php
  require('head.php');
  require('function.php');
  require('header.php');

  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
  debug('退会ページです');
  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();
loginCheck();

  if(!empty($_POST)){
    debug('POST送信があります');
    try{
    $dbh = dbConnect();
    $sql = 'UPDATE users SET delete_flg = 1 WHERE id = :user_id';
    $sql2 = 'UPDATE favorite SET delete_flg = 1 WHERE user_id = :user_id';
    $data = array(':user_id' => $_SESSION['user_id']);
    $stmt1 = queryPost($dbh,$sql,$data);
    $stmt2 = queryPost($dbh,$sql2,$data);
      if(stmt1){
        session_destroy();
        debug(print_r.$_SESSION,true);
        debug('退会に成功しました。トップページへ遷移します。');
        header('Location:index.php');
      }else{
        debug('クエリが失敗しました');
        $err_msg['common'] = msg06;
      }
  }catch(Exception $e){
    error_log('エラー発生'.$e->getMessage());
    $err_msg['common'] = msg06;
    }
  }
?>
<!--モーダル-->
    <div class="modal-panel">
    <form method="post" action="" class="modal-menu">
    <h2 id = unsubscribe-form-title>本当に退会しますか？</h2>
    <input class = "unsubscribe-btn" type="submit" value = 'はい' name = YES>
    <button type="button" class =" back-btn">いいえ</button>
    </form>
    </div>
    <div class="modal"></div>
<!--最初から表示されている箇所-->
<section id = unsubscribe>
  <form action="" id = unsubscribe-form>
  <div class="wrap">
  <div>退会を希望される方は<br>
   パスワードを入力してください</div>
    <div class="form-msg"></div>
   <input type="password" class="unsubscribe-password">
    <button type="button" class="unsubscribe-btn js-click-unsubscribe" disabled>退会する</button>
  </div>
  </form>
</section>
<?php require('footer.php')?>
