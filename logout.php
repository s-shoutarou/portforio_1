<?php
$site_title = 'ログアウト';

require('function.php');
require('header.php');
require('head.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ログアウトを実行します');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

session_destroy();

debug('トップページへ遷移します');
header('Location:index.php');
?>


<?php
  require('footer.php');
  ?>