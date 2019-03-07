<footer>
</footer>
 
  <script src = "jquery-3.3.1.min.js"></script>
  <script>
    
    $(function(){//入力フォーム初期表示
      $('.post-head').one("click",function(){
        $(this).val("");
      });
      
      //お気に入り機能
      var $fav,likeThreadId;
      
      $fav = $('.js-click-fav') || null;
      
      likeThreadId = $fav.data('threadid') || null;
      
      if(likeThreadId !== undefined && likeThreadId !== null){
        $fav.on('click',function(){
          var $this = $(this);
          $.ajax({
            type: "POST",
            url: "ajaxFav.php",
            data: {threadId : likeThreadId}
            }).done(function(data){//成功したとき
            console.log('ajax success');
            console.log($this);
            $this.toggleClass('active');
            //クラス属性の着脱をtoggleで操作
          }).fail(function(msg){
                  console.log('AjaxError');
                  });
          });
        };
    });
  </script>


</body>
</html>
