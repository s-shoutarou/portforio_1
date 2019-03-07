<?php
require('function.php');
require('head.php');
require('header.php');
loginCheck();
debugLogStart();
$favoriteBord = getFavariteBord($_SESSION['user_id']);
debug('お気に入りスレッド取得結果'.print_r($favoriteBord,true));

?>
<section class="fav-bord">
      <div class = 'entry-top'>
    <h1>お気に入り</h1>
   <?php foreach($favoriteBord as $key => $val):?>
   <?php $linkBordData = getFavLinkData($val['bord_id']);?>
        <h2 class = thread-title><?php if(!empty($fval['title'])){echo $val['title'];}?></h2>
    <img src="<?php if(!empty($val['pic'])){echo $val['pic'];}?>" alt="">
    <a href="bord.php?b_id=<?php echo $val['bord_id']."&p=".$linkBordData?>">続きを読む</a>
    <a href="b">もっとお気に入りスレッドを見る</a>
    <div><?php echo $val['title']?></div>
    </div>
  <?php endforeach?>
</section>
  
<section class="my-bord">
    <h1>スレ立て履歴</h1>
</section>
