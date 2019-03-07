<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ログアウトを実行します');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

session_destroy();

debug('トップページへ遷移します');
header('Location:index.php');
?>