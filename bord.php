<?php 
require('function.php');
require('head.php');
require('header.php');
require('side-bar.php');

loginCheck();

//カテゴリー検索を実行した場合
if(!empty($_GET['category'])){
  header('Location:index.php?category='.$_GET['category']);
}

//getパラメータ取得
$b_id = (!empty($_GET['b_id'])) ? $_GET['b_id'] : "";
$p_id = (!empty($_GET['p'])) ? $_GET['p'] : "";
//掲示板情報取得
$bord = getBord($_GET['b_id']);

debug(print_r($bord,true));

//未実装if(!empty($_GET['b_id'])){};

//レス投稿
if(!empty($_POST)){
  debug('POST送信があります');
  debug('ポストの内容'.print_r($_POST,true));
  if(!empty($_POST['post_msg'])){
    validEmpty($_POST['post_msg'],'post_msg');
    }
  if(!empty($err_msg)){
    debug('投稿フォームが不適切な状態です。');
    debug('投稿を中止します。');
    }else{
      try{
        $dbh = dbConnect();
        $sql = 'INSERT INTO message(bord_id,message,user_id,create_time) VALUES(:bord_id,:message,:user_id,:create_time)';
        $data = array(':bord_id'=>$bord['id'],':message'=>$_POST['post_msg'],':user_id'=>$_SESSION['user_id'],':create_time'=>date('Y-m-d H:i:s'));
        if($stmt = queryPost($dbh,$sql,$data)){
          debug('新規メッセージ投稿成功');
        }else{
        debug('新規メッセージ投稿失敗');
        };
    }catch(Exception $e){
        error_log($e -> getMessage());
      }
    }
}


//既存の書き込み表示
$bord_msg = getMessage($b_id);
debug('既存の書き込み取得内容'.print_r($bord_msg,true));

debugLogStart();


?>
<div class="space"></div>
<section class = bord>
<h2 class = thread-title><?php echo $bord['title']; ?></h2>
<i class="fas fa-star fa-2x fav-icon js-click-fav <?php if(isLike($_SESSION['user_id'],$b_id)){echo "active";}?>" data-threadid = "<?php echo $b_id;?>"></i>
 <div class="entry-top">
    <div class="bord-category"><a class = "category-link" href="index.php<?php echo "?category=".$bord['category']?>"><?php echo $bord['c_name'];?></a></div>
    <div class = pic>
    <img src=<?php echo $bord['pic']?> alt="" >
    </div>
    <div class="msg">
    <div class="name"><?php echo $bord['name']?></div>
    <div class = time><?php echo $bord['create_time']?></div>
    <?php echo $bord['first_msg']?></div>
 </div>
 <?php foreach($bord_msg as $key => $val):?>
    <div class="msg">
    <div class="name"><?php echo $val['name']?></div>
    <div class="create-time"><?php echo $val['create_time']?></div>
    <?php echo $val['message']?></div>
 <?php endforeach?>
</section>

<div class = "back"><a href="index.php<?php appendGetParam(array('b_id'));?>">&lt;掲示板一覧に戻る</a></div>

<!--レス投稿フォーム-->
<div>
<section class = post-form>
    <form action="" method ="post" enctype ="multipart/form-data" class = post>
    <!--<input ・type="text" name = 'user_name' class = post-head value="ハンドルネームを入力してください">-->
    <h2 class="post-top">掲示板に書き込む</h2>
        <div class = "upload_pic_form">添付ファイル
        <input type="hidden" name = "MAX_FILE_SIZE" value = "3145728">
        <input type="file" name = "pic" class = "input-file">
        </div>
      <textarea name="post_msg" class="post-main" cols="100" rows="20"></textarea>
      <input type="submit" value="送信" class = post-btn>
    </form>
</section>
</div>

<?php require('footer.php');?>
