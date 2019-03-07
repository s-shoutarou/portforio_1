<?php
  $category = getCategory();
?>
 <section id = side-bar>
  <h2 class = category>カテゴリー</h2>
    <form name = "" method = post class = categoryChoice>
      <select name="choice" class="choice">
        <option value=0 <?php ?>>選択してください</option>
        <?php foreach($category as $key => $val):?>
          <option value="<?php echo $val['id']?>"><?php echo $val['name']?></option>
        <?php endforeach?>
      </select>
    <input type="submit" value = "検索">
    </form>
</section>
