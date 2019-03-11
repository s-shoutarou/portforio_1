 <header>
<section id = header>
<ul id = head-menu>
 <li>
  <nav class = logo>
    <h1 class = logo><a href="index.php">将棋場〜</a></h1>
  </nav>
  </li>
     <nav>
        <li class = menu ><a href="index.php" class=link>トップ</a></li>
        <?php if(empty($_SESSION['user_id'])){ ?>
        <li class = menu><a href="login.php" class=link>ログイン</a></li>
        <li class = menu><a href="signup.php" class=link>新規登録</a></li>
        <?php }else{ ?>
        <li class = menu><a href="mypage.php" class=link>マイページ</a></li>
        <li class = menu><a href="logout.php" class=link>ログアウト</a></li>
        <li class = menu><a href="unsubscribe.php" class = link>退会</a></li>
        <?php } ?>
      </ul>
    </nav>
  </section>
</header>