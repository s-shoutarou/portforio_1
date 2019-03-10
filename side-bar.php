<?php
  $category = getCategory();
?>

<?php if(basename($_SERVER['PHP_SELF']) !== 'mypage.php'): //マイページ以外の場合?>
 <section id = side-bar>
  <h2 class = category>カテゴリー</h2>
    <form name = "" method = post class = categoryChoice>
      <select name="category" class="choice">
        <option value=0 <?php ?>>選択してください</option>
        <?php foreach($category as $key => $val):?>
          <option value="<?php echo $val['id']?>"><?php echo $val['name']?></option>
        <?php endforeach?>
      </select>
    <input type="submit" value = "検索">
    </form>
</section>


<?PHP 
  //未使用
  else://マイページの場合?>
 <section id = side-bar>
  <h2 class = user-menu>MENU</h2>
  <ul>
    <li class="user-info"><a href="userInfo.php">登録情報変更</a></li>
  </ul>
</section>
<?php endif?>