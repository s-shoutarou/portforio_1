<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' Ajax処理');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

if(isset($_POST['threadId']) && isset($_SESSION['user_id']) && isLogin()){
  debug('POST送信があります');
  $b_id = $_POST['threadId'];
  debug('スレッドID：'.$b_id);
  try{
    debug('お気に入り状態確認開始');
    $dbh = dbConnect();
    $sql = 'SELECT * FROM favorite WHERE bord_id = :b_id AND user_id = :u_id' ;
    $data = array(':b_id'=>$b_id,':u_id'=>$_SESSION['user_id']);
    $stmt = queryPost($dbh,$sql,$data);
    $resultCount = $stmt -> rowCount();
    debug($resultCount);
    debug('お気に入り状態確認完了');
    if(!empty($resultCount)){
      debug('お気に入りを解除します');
      $sql = 'DELETE FROM favorite WHERE bord_id = :b_id AND user_id = :u_id';
      $data = array(':b_id'=>$b_id,':u_id'=>$_SESSION['user_id']);
      $stmt = queryPost($dbh,$sql,$data);
    }else{
      debug('お気に入りに追加します');
      $sql = 'INSERT INTO favorite (bord_id,user_id,create_time) VALUES (:b_id,:user_id,:c_date)';
      $data = array('b_id'=>$b_id,':user_id'=>$_SESSION['user_id'],':c_date'=>date('Y-m-d H:i:s'));
      $stmt = queryPost($dbh,$sql,$data);
    }
  }catch(Exception $e){
    error_log('エラー:'.$e->getMessage());
  }
}
?>