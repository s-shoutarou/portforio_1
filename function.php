<?php
ini_set('log_errors','on');
ini_set('erro_log','php.log');

$debug_flg = true;

$err_msg = array();

//エラーメッセージ集(定数)////////////////////////////////////////////////////////////////
define ('msg01','emailの形式ではありません');
define ('msg02','文字数が限界を超えています');
define ('msg03','文字数が少なすぎます');
define ('msg04','何も入力されていません');
define ('msg05','パスワード、またはメールアドレスが間違っています');
define ('msg06','エラーが発生しました。');
define ('msg07','全角文字を使用しています。');
define ('msg08','パスワード、またはパスワードの再入力が誤っています');
define ('msg09','そのemailはすでに登録されています');
define ('msg10','スレッド名を入力してください');
define ('msg11','png,jpg,jpeg以外の形式で保存されたファイルはアップロード出来ません');
define ('msg12','画像の容量が大きすぎます');

function debug($str){
  global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ:'.$str);
    }
  }

session_save_path("/var/tmp/");

ini_set('session.gc_maxlifetime', 60*60*24*30);

ini_set('session.cookie_lifetime',60*60*24*30);

session_start();
//セッション
//login_time,login_limit
//

session_regenerate_id();

//デバッグ表示////////////////////////////////////////////////////////////////
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時のタイムスタンプ：'.time());
  if(!empty($_SESSION['login_time']) && !empty($_SESSION['login_limit'])){
    debug('ログイン期限タイムスタンプ：'.($_SESSION['login_time'] + $_SESSION['login_limit']));
  }
}

//ログインチェック////////////////////////////////////////////////////////////////
function loginCheck(){
  if(!empty($_SESSION['user_id'])){
    debug('ログイン済みユーザーです。');
    if($_SESSION['login_time']+$_SESSION['login_limit']<time()){
      debug('ログイン有効期限オーバーです。');
      session_destroy();
      header("Location:login.php");
    }else{
      debug('ログイン有効期限内です');
      $_SESSION['login_time']=time();
      if(basename($_SERVER['PHP_SELF']) === 'login.php'){
        debug('マイページへ遷移します。');
        header('Location:mypage.php');
      }
    }
  }else{
    debug('未ログインユーザーです。');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
    debug('ログインページに遷移します');
    header('Location:login.php');
    }
  }
}

//ログインチェック遷移なし

function isLogin(){
  if(!empty($_SESSION['login_time'])){
    debug('ログイン済みユーザーです。');
  
    if(($_SESSION['login_time'] + $_SESSION['login_limit']) < time()){
    debug('ログイン有効期限オーバーです');
    session_destroy();
    return false;
      }else{
        debug('ログイン有効期限以内です');
        return true;
      }
  }else{
      debug('未ログインユーザーです');
      return false;
    }
  }
//ヴァリデーション////////////////////////////////////////////////////////////////
function validEmail($str){
  global $err_msg;
  if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)) {
    $err_msg['email'] = msg01;
  }
}

function validMax($str,$key,$max = 256){
  global $err_msg;
  if(mb_strlen($str)>$max){
    $err_msg[$key] = msg02;
  }
}

function validMinimum($str,$key,$min = 4){
  global $err_msg;
  if(mb_strlen($str) < $min){$err_msg[$key] = msg03;}
}

function validEmpty($str,$key){
  global $err_msg;
  if(empty($str)){$err_msg[$key] = msg04;}
}

function validHalf($str,$key){
  global $err_msg;
  if(!preg_match("/^[a-zA-Z0-9]+$/",$str)){
    $err_msg[$key] = msg07;
  }
}

function validMatch($str,$str_retype){
  global $err_msg;
  if($str !== $str_retype){
    $err_msg['pass_retype'] = msg08;
  }
}

function validThreadTitle($str){
  global $err_msg;
  if($str == 'スレッド名を入力してください'){
    $err_msg['thread_title'] = msg10;
  }
}

function validFormatCheck($pic){
  global $err_msg;
  if(!preg_match('/\.png$|\.jpg$|\.jpeg$/i',$pic)){
    debug('png,jpg,jpeg以外の形式で保存されたファイルはアップロード出来ません');
    $err_msg['pic'] = msg11;
  }
}

function validPicMax($pic){
  global $err_msg;
  if($pic>2000000){
  $err_msg['pic'] = msg12;
  debug('ファイルの容量が大きすぎます');
  }
}
//DB接続////////////////
function dbConnect(){
  $dsn = 'mysql:dbname=shougi;host=localhost;charset=utf8';
  $user = 'root';
  $pass  = 'root';
  $options = array(
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::MYSQL_ATTR_USE_BUFFERED_QUERY =>true,
    );
  $dbh = new PDO($dsn,$user,$pass,$options);
  debug('DBコネクト成功');
  return $dbh;
}
                      
function queryPost($dbh,$sql,$data){
  $stmt = $dbh->prepare($sql);
  debug('クエリ内容確認：'.print_r($stmt,true));
  if(!$stmt->execute($data)){
    debug('クエリに失敗しました');
    debug('失敗したSQL:'.print_r($stmt,true));
    $err_msg['common'] = msg06;
    return 0;
  }
    debug('クエリ成功');
    return $stmt;
}

function validDuplication($str){
  global $err_msg;
  try{
    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $str);
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt -> fetch(PDO::FETCH_ASSOC);
      if(!empty($result['count(*)'])){
        debug('被りを検出しました');
        $err_msg['email'] = msg09;
      }
  }catch (Exeption $e){
        error_log('エラー'.$e -> getMessage());
        $err_msg['common'] = msg06;
        debug('被りチェックエラーが起きてます');
      }
  }

//サニタイズ
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}

//トップページ表示用掲示板データ取得
function getBordDataList($currentMinNum = 1 , $span = 10){
  debug('掲示板情報を取得します');
  try{
    $dbh = dbConnect();
    //スレッド数の取得
    $sql = 'SELECT id FROM bord';
    $data = array();
    $stmt = queryPost($dbh,$sql,$data);
    $rst['total'] = $stmt->rowCount();//総レコード数
    $rst['total_page'] = ceil($rst['total']/$span);//総ページ数
    if(!$stmt){
      debug('falseを返します');
      return false;
    }
    //ページング用SQL文作成
    $sql = 'SELECT * FROM bord ORDER BY id DESC ';
    $sql.= 'LIMIT :span OFFSET :currentMinNum';
    debug('SQL:'.$sql);
    $stmt = $dbh->prepare($sql);
    debug('クエリ内容確認：'.print_r($stmt,true));
    $stmt->bindValue(':span',(int)$span,PDO::PARAM_INT);
    $stmt->bindValue(':currentMinNum',(int)$currentMinNum,PDO::PARAM_INT);
    if($stmt->execute()){
      //クエリ結果のレコードを全て格納
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

// 個別スレッドの詳細情報取得
function getBord($b_id){
  global $err_msg;
  try{
  $dbh = dbConnect();
  $sql = 'SELECT bord.id,bord.create_time,title,category,category.name as c_name,pic,first_msg,user_id,users.name FROM bord INNER JOIN category ON bord.category =category.id INNER JOIN users ON bord.user_id = users.id WHERE bord.id =:id';
    $data =array(':id'=>$b_id);
  $stmt = queryPost($dbh,$sql,$data);
  return $stmt->fetch(PDO::FETCH_ASSOC);
    
  }catch(Exception $e){
    error_log('エラー'.$e->getMessage());
    $err_msg['common'] = msg06;
  }
}

function getMessage($str){
  try{
  $dbh = dbConnect();
  $sql = 'SELECT * FROM message INNER JOIN users ON message.user_id = users.id WHERE message.bord_id = :bord_id ';
  $data = array(':bord_id'=>$str);
  $stmt = queryPost($dbh,$sql,$data);
    if($stmt){
      debug('既存メッセージの取得に成功');
      return $result = $stmt -> fetchAll();
    }else{
      debug('既存メッセージの取得に失敗');
    }
  }catch(Exception $e){
    error_log('エラー：'.$e -> getMessage());
  }
}
//スレッド立て・編集関連
//戦型取得
function getCategory(){
  debug('カテゴリーを取得します');
  try{
  $dbh = dbConnect();
  $sql = 'SELECT * FROM category';
  $data = array();
  $stmt = queryPost($dbh,$sql,$data);
  if($stmt){
  debug('カテゴリーの取得に成功しました。');
  return $result = $stmt->fetchAll();
  }else{
    debug('カテゴリーの取得に失敗しました');
    return false;
  }
  }catch(Exception $e){
  error_log('エラー発生'.$e->getMessage());
  }
}

function getProduct($u_id,$b_id){
  debug('掲示板情報を取得します');
  debug('ユーザーID'.$u_id);
  debug('掲示板ID'.$b_id);
  try{
  $dbh = dbConnect();
  $sql = 'SELECT * FROM bord WHERE id = :bord_id AND parent_id = :user_id';
  $data = array(':bord_id'=>$b_id,':user_id'=>$u_id);
  $stmt = queryPost($dbh,$sql,$data);
  debug();
  }catch(Exception $e){
    '';
  }
}

function uploading($pic,$key){
  try{
    switch($pic['error']){
      case UPLOAD_ERR_OK: //
        break;
      case UPLOAD_ERR_NO_FILE://ファイル未選択
        throw new RuntimeException('ファイルが選択されていません');
      case UPLOAD_ERR_INI_SIZE://php.ini定義の最大サイズを超えている
      case UPLOAD_ERR_FORM_SIZE://フォーム定義の最大サイズを釣果
        throw new overflowexception('ファイルサイズが大きすぎます');
      default:
          throw new runtimeexception('その他のエラーが発生しました');
    }
    
    //MIMEタイプのチェック
    $type = @exif_imagetype($pic['tmp_name']);
    if(!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],TRUE)){//第三引数にはtrueを指定て厳密にチェックさせる
      throw new runtimeexception('画像形式が未対応です');
    }
      $path = 'uploads/'.sha1_file($pic['tmp_name']).image_type_to_extension($type);
      if(!move_uploaded_file($pic['tmp_name'],$path)){//ファイルを移動する
        throw new runtimeexception('ファイル保存時にエラーが発生しました');
      }
        //保存したファイリのパーミッション(権限)を変更する
        chmod($path,0644);
        
        debug('ファイルは正常にアップロードされました');
        debug('ファイルパス:'.$path);
        return $path;
    }catch(runtimeexception $e){
    debug($e->getMessage());
    global $err_msg;
    $err_msg[$key] = $e->getMessage();

  }catch(overflowexception $e){
    debug($e->getMessage());
    global $err_msg;
    $err_msg[$key] = $e->getMessage();
  }
}

function buildNewThread($content,$u_id,$p_path,$p_id){//実際のスレ立て
  try{
  $dbh = dbConnect();
  $sql = 'INSERT into bord (title,category,pic,first_msg,user_id,create_time) VALUES (:title,:category,:pic,:f_msg,:u_id,:c_time)' ;
  $data = array(':title'=>$content['thread_title'],'category'=>$content['c_id'],':pic'=>$p_path,':f_msg'=>$content['first_msg'],'u_id'=>$u_id,
                ':c_time'=>date('Y-m-d H:i:s'));
  queryPost($dbh,$sql,$data);
  debug('新規スレッド情報の登録に成功しました');
    //スレッドに飛ぶ
    debug('新規スレッドへ遷移します');
    $sql2 = 'SELECT id FROM bord ORDER BY id DESC';
    $data2 = array();
    $stmt = queryPost($dbh,$sql2,$data2);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  header('Location:bord.php?b_id='.$result['id'].'&p='.$p_id);
  }catch(Exception $e){
    error_log($e->getMessage());
    debug('新規スレッド情報のDB登録に失敗しました');
  }
}

function appendGetParam($arr_del_key){
  debug('アペンド第０段階・$arr_del_key'.print_r($arr_del_key,true));
  if(!empty($_GET)){
    debug('アペンド第一段階'.print_r($_GET,true));
    $str = '?';
    foreach($_GET as $key => $val){
      debug(print_r($_GET,true));
      debug('$key='.$key);
      debug('$val='.$val);
      if(!in_array($key,$arr_del_key,true)){//取り除きたいパラメータでない場合にurlに付与するパラメータを作成
        $str .= $key.'='.$val.'&';
        debug('アペンド第２段階'.$str);
      }
    }
    $str = mb_substr($str,0,-1,"UTF-8");
    debug('アペンド第３段階'.$str);
    echo $str;
  }
}

//マイページ用
function getFavariteBord($u_id){
  try{
  $dbh = dbConnect();
  $sql = 'SELECT * FROM favorite INNER JOIN bord ON favorite.bord_id = bord.id WHERE favorite.user_id = :user_id';
  $data = array(':user_id'=>$u_id);
  $stmt = queryPost($dbh,$sql,$data);
  return $stmt->fetchAll();
}catch(Exception $e){
  error_log('エラー：'.$e->getMessage());
  return false;
  }
}

function isLike($u_id,$b_id){
  try{
    $dbh = dbConnect();
    $sql ='SELECT* FROM favorite WHERE bord_id = :bord_id AND user_id = :user_id';
    $data = array(':bord_id'=>$b_id,':user_id'=>$u_id);
    $stmt = queryPost($dbh,$sql,$data);
    if($stmt->rowCount()){
      debug('お気に入りスレッドの取得に成功しました');
      $result = $stmt->fetchAll();
      return true;
    //return $result;
      }else{
    debug('お気に入りスレッドの取得に失敗しました');
    return false;
      }
    }catch(Exception $e){
    error_log('エラー'.$e -> getMessage());
    debug('データの取得に失敗しました');
  }
}

//お気に入り一覧URL作成用関数
//トップページ表示用掲示板データ取得
function getFavLinkData($b_id,$span = 10){
 $page_num = ceil($b_id/$span);
  debug('$page_num:'.$page_num);
    debug('掲示板一覧へ遷移するためのgetパラメータを作成します');
    return $page_num;
  }

function getSelectBord($str){
  try{
  $dbh = dbConnect();
  $sql = 'SELECT * FROM bord WHERE category = :category ORDER BY id DESC';
  $data = array(':category'=>$str);
  $stmt = queryPost($dbh,$sql,$data);
    
                    
  if($stmt){
  $bordData = $stmt->fetchAll();
  $bordCount = $stmt->rowCount();
  return array('bordData'=>$bordData,'bordCount'=>$bordCount);
    }else{
      debug('選択した戦型のスレッドデータを取得できませんでした');
      return false;
    }
  }catch(Exception $e){
    error_log('エラー：'.$e->getMessage());
  }
}
?>