<?php
require('function.php');

if(!empty($_POST)){
  try{
  debug('送信された値:'.print_r($_POST,true));
  $pass = $_POST['pass'];
  $dbh = dbConnect();
  $sql = 'SELECT pass FROM users WHERE id = :id AND delete_flg = 0';
  $data = array(':id'=>$_SESSION['user_id']);
  $stmt = queryPost($dbh,$sql,$data);
  $get_pass = $stmt -> fetch(PDO::FETCH_ASSOC);
  debug('取得パス'.print_r($get_pass,true));
  $result = password_verify($pass,array_shift($get_pass));
  if($result){
    debug('pass and email 一致');
    $result = array('bool'=>true);
    echo json_encode($result);
  }else{
    debug('pass 不一致');
    $result = array('msg'=>'passwordが違います',
               'bool'=>false);
    echo json_encode($result);
    }
  }catch(Exception $e){
    error_log('エラーメッセージ:'.$e->getMessage());
  }
}
?>