@extends('user.layouts.base')

@section('title', 'コメント一覧')

@section('content')
<section class="section_base">
  <div class="tit_L_01 E-font">
      <h2>COMMENTS</h2>
      <div class="sub_tit_L">コメント一覧</div>
  </div>

<!-- ------------------------------------------------------------------------------------------ -->
  <div class="prof_page_base inner_item">
    <div class="comment_page">
      <div class="prof_edit_row" style="{{ isset($test) ? '' : 'border-bottom: none;' }}">
          <img src="/storage/sampleImage/my-page.svg" alt="" class="user_image">
          <div class="comment">応援しています。頑張ってください。応援しています。頑張ってください。応援しています。頑張ってください。応援しています。頑張ってください。<br>
            <div>
              <span class="comment_information">山田 太郎&emsp;</span><span>コメント時刻 : 12:00</span>
              <a href=""><i class="fas fa-chevron-circle-down fa-lg fa-fw icons_mobile"></i></a>
            </div>

          </div>
          <div class="icons_pc">
              <a href=""><i class="fas fa-reply fa-2x fa-fw"></i></a>&emsp;
              <a href=""><i class="far fa-trash-alt fa-2x fa-fw"></i></a>
          </div>
      </div>
      
      <div class="prof_edit_row">
          <img src="/storage/sampleImage/my-page.svg" alt="" class="user_image reply_user">
          <div class="comment reply">応援しています。頑張ってください。応援しています。頑張ってください。応援しています。頑張ってください。応援しています。頑張ってください。<br>
            <div>
              <span class="comment_information">山田 太郎&emsp;</span><span>コメント時刻 : 12:00</span>
              <a href=""><i class="fas fa-chevron-circle-down fa-lg fa-fw icons_mobile"></i></a>
            </div>
          </div>
          <div class="icons_pc">
              <a href=""><i class="far fa-trash-alt fa-2x fa-fw"></i></a>
          </div>
      </div>
    </div>
  </div>

  <!-- ------------------------------------------------------------------------------------------ -->

</section>
@endsection

<style>
.comment_page{ 
  width: 100%;
}

.user_image{
  border-radius: 50%;
}

.comment{
  width: 80%;
  line-height: 35px;
}

.comment div{
  font-size: 85%;
}

.icons_pc{
  color: #00AEBD;
}

.icons_mobile{
  color: #00AEBD;
  visibility:hidden;
}

.comment_information{
  color: #00AEBD;
}

.reply{
  width: 790px;
}

.reply_user{
  margin-left: 65px;
}

@media (max-width: 767px) {
  .user_image{ margin: 30px 0 10px 0; }
	.reply{ width: calc(100% - 75px);} 
  .reply_user{ margin-left: 0px; }
  .icons_pc{ margin: 20px 0 40px 0; display: none; }
  .icons_mobile{ 
    visibility: visible;
    position: relative;
    left: calc(100% - 235px);
  }
}
</style>