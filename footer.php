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
//ボタン押下ブロック
  var textData = {email:'',pass:''};
  $('.unsubscribe-password').on('keyup',function(){
    textData['pass'] = $('.unsubscribe-password').val();
    console.log(textData);
    $.ajax({
      type:'post',
      url:'ajaxBtnBlock.php',
      dataType:'json',
      data:textData
    }).done(function(result){
      console.log(result);
      (result.msg) ? $('.form-msg').text(result.msg) : '';
      if(result.bool){
      $('.form-msg').text('')
      $('.js-click-unsubscribe').prop('disabled',false);
      $('.js-click-unsubscribe').toggleClass('unsubscribe-btn');
      console.log('btnを押せるようになりました');}
      else{
      $('.js-click-unsubscribe').prop('disabled',true);}
    }).fail(function(result,textStatus,errorThrown){
      console.log('通信に失敗しています');
      console.log(result);
      console.log(textStatus);
      console.log(errorThrown);
    })
  })
  
  
      //引き止めモーダル
      var windowWidth = $(window).innerWidth();
      var windowHeight = $(window).innerHeight();
      $('.js-click-unsubscribe').on("click",function(){
          $('.modal').css('width',windowWidth + "px");
          $('.modal').css('height',windowHeight + "px");
          $('.modal-panel,.modal').css('display','block');
        $('.back-btn').on("click",function(){
          $('.modal-panel,.modal').css('display','none');
        })
        }
      )}
      );
  </script>


</body>
</html>
