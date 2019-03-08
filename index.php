<?php
require('function.php');
require('head.php');
require('header.php');
require('side-bar.php');
//loginCheck();
debugLogStart();

  //$p_id = (!empty($_GET)) ? $_GET['p_id'] : '';
  $dbFormData = (!empty($p_id)) ? getBord($_SESSION['user_id'],$b_id) : '';

//$category = getCategory();

//ページネーション///////////////////////////////////////////////////////
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;//デフォルトは1ページ目
//パラメータに不正な値が入っているかチェック
if(!is_int((int)$currentPageNum)){
  error_log("エラー発生：指定ページに不正な値が入りました");
  header('Location:index.php');//トップページへ
}

//スレッドの表示件数
$listSpan = 10;

//現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum - 1)*$listSpan);
//DBからスレッドデータを取得
$dbBordData = getBordDataList($currentMinNum);


//新規スレッド作成関係//////////////////////////////////////
if(!empty($_POST && $_FILES)){
  debug('POST送信があります');
  $thread_title = $_POST['thread_title'];
  $senkei = $_POST['c_id'];
  $pic = $_FILES['pic'];
  $first_msg = $_POST['first_msg'];

  $postContent = $_POST;
  $picContent = $_FILES;
  debug('送信した値'.print_r($postContent,true));
  debug('送信した値'.print_r($picContent,true));
  
//スレタイチェック//////////////////////////////////////////////////////////////////////////////////
  validEmpty($thread_title,'thread_title');
  validThreadTitle($thread_title);
//本文チェック//////////////////////////////////////////////////////////////////////////////////
  validMax($thread_title,'thread_title',2000);

  validMinimum($thread_title,'thread_title');

  validEmpty($thread_title,'thread_title');
  
//カテゴリーチェック
  validEmpty($senkei,'senkei');
//画像チェック
  $pic_path = uploading($pic,'pic');
  //validEmpty($pic,'pic');
  //validPicMax($pic['size']);
  //validFormatCheck($pic['name']);
//本文チェック
  validEmpty($first_msg,'first_msg');
  
  debug('バリデーション終了');
  
  if(!empty($err_msg)){
  debug(print_r($err_msg,true));
  }
  
  if(empty($err_msg)){
    debug('新規スレッド情報をDBに保存します');
    buildNewThread($postContent,$_SESSION['user_id'],$pic_path,$currentPageNum);
  }
}

//戦型選択検索
if((!empty($_POST['category']))){
  $category = $_POST['category'];
  debug('戦型番号'.$category);
  $categoryChoiceBord = getSelectBord($category);
  debug('戦型選択スレッド取得結果'.print_r($categoryChoiceBord,true));
}

?>

<body>

<?php if(empty($categoryChoiceBord)):?>
<div class="entry-top">
  <div>
  <span class = total><?php echo sanitize($dbBordData['total'])?></span>件のスレッドが見つかりました
  </div>
  <div>
    <span class = num><?php echo $currentMinNum +1;?></span>-<span class = num><?php echo $currentMinNum + $listSpan?></span>件 / <span class=num><?php echo sanitize($dbBordData['total'])?></span>件中
  </div>
  <!--掲示板一覧表示-->
  <?php foreach($dbBordData['data'] as $key => $val):?>
  <section class = bord>
    <h2 class = thread-title><a href="bord.php?b_id=<?php echo $val['id']."&p=".$currentPageNum ;?>"><?php echo $val['title']?></a> </h2>
  <i class="fas fa-star fa-2x fav-icon" data-thread-id = "<?php echo $val['id'];?>"></i>
    <a href="bord.php?b_id=<?php echo $val['id']."&p=".$currentPageNum ;?>"><img src="<?php echo $val['pic']?>" alt=""></a>
    <a href="bord.php?b_id=<?php echo $val['id']."&p=".$currentPageNum ;?>">続きを読む</a>
  </section>
</div>
  <?php endforeach?>
<!--戦型選択をした場合-->
<?php else:?>
<div class="entry-top">
  <div>
  <span class = total><?php echo sanitize($categoryChoiceBord['bordCount'])?></span>件のスレッドが見つかりました
  </div>
  <div>
    <span class = num><?php echo $currentMinNum +1;?></span>-<span class = num><?php echo $currentMinNum + $listSpan?></span>件 / <span class=num><?php echo sanitize($categoryChoiceBord['bordCount'])?></span>件中
  </div>
  <!--掲示板一覧表示-->
  <?php foreach($categoryChoiceBord['bordData'] as $key => $val):?>
  <section class = bord>
    <h2 class = thread-title><a href="bord.php?b_id=<?php echo $val['id']."&p=".$currentPageNum ;?>"><?php echo $val['title']?></a> </h2>
  <i class="fas fa-star fa-2x fav-icon" data-thread-id = "<?php echo $val['id'];?>"></i>
    <a href="bord.php?b_id=<?php echo $val['id']."&p=".$currentPageNum ;?>"><img src="<?php echo $val['pic']?>" alt=""></a>
    <a href="bord.php?b_id=<?php echo $val['id']."&p=".$currentPageNum ;?>">続きを読む</a>
  </section>
</div>
    <?php endforeach?>
<?php endif?>


<!--ページネーション-->
<div class="pagenation">
  <ul class="pagenation-list">
    <?php 
    $pageColNum = 5;
    $totalPageNum = $dbBordData['total_page'];
    //現在のページが総ページ数と同じかつ総ページ数が表示項目数以上なら、左にリンクを4つ表示する。
    if($currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum){
      $minPageNum = $currentPageNum - 4;
      $maxPageNum = $currentPageNum;
    //現在のページ数が総ページ数の1ページ前なら左にリンク3個、右にリンク１個を表示する。
    }else if($currentPageNum == $totalPageNum - 1 && $totalPageNum >= $pageColNum){
      $minPageNum = $currentPageNum - 3;
      $maxPageNum = $currentPageNum + 1;
    //現在のページ数が2の場合は左にリンク1個、右にリンク3個を表示する。
    }else if($currentPageNum == 2 && $totalPageNum >= $pageColNum){
      $minPageNum = $currentPageNum -1;
      $maxPageNum = $currentPageNum + 3;
    //現ページが1の場合は左に何も出さず、右にリンクを5個表示する。
    } else if($currentPageNum == 1 && $totalPageNum >= $pageColNum){
      $minPageNum = $currentPageNum;
      $maxPageNum = 5;
    //総ページ数が表示項目数より少ない場合は、総ページ数をループのMax、ループのMinを1に設定。
    }else if($totalPageNum < $pageColNum){
      $minPageNum = 1;
      $maxPageNum = $totalPageNum;
    //それ以外の場合は左右に2個ずつリンクを表示する
    }else{
      $minPageNum = $currentPageNum - 2;
      $maxPageNum = $currentPageNum + 1;
    }
    ?>
    
    <?php if($currentPageNum != 1):?>
      <li class="list-item"><a href="?p=1">&lt;</a></li>
    <?php endif; ?>
    <?php
    for($i = $minPageNum;$i<= $maxPageNum;$i++):
    ?>
    <li class="list-item <?php if($currentPageNum == $i) echo 'active';?>"><a href="?p=<?php echo $i ;?>"><?php echo $i ;?></a></li>
    <?php endfor;?>
    <?php if($currentPageNum == $minPageNum):?>
      <li class ="list-item"><a href="?p=<?php echo $maxPageNum; ?>">&gt;</a></li>
    <?php endif;?>
  </ul>
</div>


<div id = build-new-thread>
<section class = post-form>
 <div class = build-new-thread-logo>新しいスレッドを立てる</div>
    <form action="" method ="post" enctype ="multipart/form-data" class = post>
      <input type="text" name = 'thread_title' class = post-head value="スレッド名を入力してください">
        <select name="c_id" id="build-new-thread-logo-choice">
          <option value="0">
          カテゴリーを選んでください
          </option>
          <?php foreach(getCategory() as $key => $value){?>
            <option value="<?php echo $value['id']?>"><?php echo $value['name']?></option>
          <?php }?>
        </select>
        <div class = "upload_pic_form">添付ファイル
        <input type="hidden" name = "MAX_FILE_SIZE" value = "3145728">
        <input type="file" name = "pic" class = "input-file">
        </div>
      <textarea name="first_msg" class="post-main" cols="100" rows="20"></textarea>
      <input type="submit" value="送信" class = post-btn>
    </form>
</section>
</div>



<?php
  require('footer.php');
  ?>