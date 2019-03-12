<footer>
</footer>
 
  <script src = "jquery-3.3.1.min.js"></script>
  <script>
    
    $(function(){
      
      //入力フォーム初期表示
      $('.post-head').one("click",function(){
        $(this).val("");
      });
      
      //お気に入り機能
      var $fav,likeThreadId;
  $('.js-click-fav').on("click",function(){
   $fav = $('.js-click-fav') || null;
   likeThreadId = $fav.data('threadid') || null;
   console.log(likeThreadId);
    if(likeThreadId !== undefined && likeThreadId !== null){
      var $this = $(this);
        $.ajax({
            type: "POST",
            url: "ajaxFav.php",
            data: {threadId : likeThreadId}
            }).done(function(data){//成功したとき
            console.log('ajax success');
            $this.toggleClass('active');
            //クラス属性の着脱をtoggleで操作
          }).fail(function(msg){
                  console.log('AjaxError');
                  });
          }
    });
      });
  </script>


</body>
</html>
