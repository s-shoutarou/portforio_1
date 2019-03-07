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
<section id = unsubscribe>
  <form action="" method="post" id = unsubscribe-form>
    <h2 id = unsubscribe-form-title>本当に退会しますか？</h2>
    <input type="submit" value = 'はい' name = submit>
  </form>
</section>
