<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<script>
var ua = navigator.userAgent.toLowerCase();
var isiOS = (ua.indexOf('iphone') > -1) || (ua.indexOf('ipad') > -1);
if(isiOS) {
  var viewport = document.querySelector('meta[name="viewport"]');
  if(viewport) {
    var viewportContent = viewport.getAttribute('content');
    viewport.setAttribute('content', viewportContent + ', user-scalable=no');
  }
}
</script>

<title>FanReturn (ファンリターン) 〜インフルエンサーの「やりたい」が叶う〜 | @yield('title')</title>
<meta name="description" content="グッズを作りたい！ファンイベントを開きたい！そんなインフルエンサーに特化したクラウドファンディングサービスです。更にファンの満足度をUPさせる仕組みが多数！">
<link rel="shortcut icon" href="{{ asset('image/fanreturn.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('image/fanreturn_apple_touch_icon.png') }}">
<link rel="icon" type="image/png" href="{{ asset('image/fanreturn_android_chrome_192x192.png') }}">

<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

{{-- toastr読み込み --}}
<script src={{ asset("js/toastr.min.js") }}></script>
{{-- toastr読み込み --}}

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
<link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css" rel="stylesheet">
<link rel='stylesheet' href={{ asset('css/toastr.min.css') }} />
<link href="{{ asset('css/reset.css') }}" type="text/css" rel="stylesheet">
<link href="{{ asset('css/style.css') }}" type="text/css" rel="stylesheet">
@yield('css')

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&display=swap" rel="stylesheet">

</head>

<body>
<div id="wrapper" class="hfeed">


<div id="header_wrap" style="">
<div id="header_banner">
    <div class="header_banner_text">
        プロジェクトを始めたい方は、まずご相談！
    </div>
    <div class="header_banner_link_wrapper">
        <a class="header_banner_link" href="https://lin.ee/JTL9trf">
            <img border="0" src="https://scdn.line-apps.com/n/line_add_friends/btn/ja.png">
        </a>
    </div>
</div>
<header id="header">

<div id="branding">
	<div id="header_01">
		<div id="site-title">
			<a href="{{ route('user.index') }}" title="FanReturn" rel="home">
				<img class="h_logo_css" src="{{ asset('image/logo-color.svg') }}">
			</a>
		</div>
	</div>
	<div id="header_03">
        <a href="{{ route('user.message.index') }}" class="header_message_icon">
            <i class="far fa-envelope"></i>
            <span class="chat_unread_count">
                327
                {{-- {{ $message->message_contents_count }} --}}
            </span>
        </a>
        <input type="checkbox" id="nav-tgl_clone" name="nav-tgl_clone" style="display: none;">
        <label for="nav-tgl" class="open nav-tgl-btn"><span></span></label>
        <label for="nav-tgl" class="close nav-tgl-btn"></label>
	</div>
</div>

<div id="menu">
<div class="global-navi">
<input type="checkbox" id="nav-tgl" name="nav-tgl">
<div class="drower-menu">
<div class="drower-menu-list">
    <nav id="js-header__nav " class="l-header__nav navgation_inner">
		<a href="{{ route('user.index') }}" title="FanReturn" rel="home" class="h_logo_css_sp">
			<img src="{{ asset('image/logo-white.svg') }}">
		</a>

        <div class="other_site_header"></div>
		<ul id="js-global-nav" class="p-global-nav main-menu menu_base taso_menu">

            <li class="menu-item nav_btn taso_li menuset_01">
                <a href="{{ route('user.my_project.project.index') }}" class="top_menu-1 nav_btn_link">
                    <p class="nav_btn_tit_L">はじめる</p>
				</a>
			</li>
			<li class="menu-item nav_btn taso_li menuset_01">
                <a href="{{ route('user.search') }}" class="top_menu-1 nav_btn_link">
                    <p class="nav_btn_tit_L">さがす</p>
				</a>
			</li>
			<li class="menu-item nav_btn taso_li menuset_01">
                <a href="{{ route('user.question') }}" class="top_menu-1 nav_btn_link">
                    <p class="nav_btn_tit_L">ファンリターンとは</p>
				</a>
			</li>
            @guest('web')
            <li class="menu-item nav_btn taso_li menuset_06">
				<a href="{{ route('login') }}" class="top_menu-1 nav_btn_link">
					<p class="nav_btn_tit_L">ログイン</p>
				</a>
			</li>
            <li class="menu-item nav_btn taso_li menuset_06">
				<a href="{{ route('user.pre_create') }}" class="top_menu-1 nav_btn_link">
					<p class="nav_btn_tit_L">新規登録</p>
				</a>
			</li>
            @endguest
            <form method="get" action="{{ route('user.search') }}" name="word_search">
                <li class="menu-item nav_btn taso_li menuset_04 header_serch_box">
                    <i class="fas fa-search"></i><input type="text" name="word" placeholder="キーワードを検索" value="{{ Request::get('word') }}">
                    <a href="javascript:word_search.submit()" class="top_menu-1 nav_btn_link signup_btn" style="justify-content: center;">
                        <p>検索</p>
                    </a>
                </li>
            </form>


			{{-- <li id="menu-item-2" class="menu-item menu-item-2 nav_btn menu-item-has-children taso_li menuset_02">
					<a href="#" id="top_menu-2" data-megamenu="js-megamenu2" class=" nav_btn_link taso_li_a">
						<label for="item_c_2" class="item_c">
							<div>
							<p class="nav_btn_tit_L">人気のタグ</p>
							</div>
						</label>
					</a>
				<input type="checkbox" id="item_c_2" style="display:none;">
				<input type="checkbox" id="ta_menu-2"><label for="ta_menu-2" class="taso_li_a_label"><span class="pd"><i class="fas fa-chevron-down"></i></span></label>
					<ul class="taso_ul pri_W_b taso_ul_ko" style="background: #fff; padding: 10px 10px 0 10px;">
						<li class="taso_li taso_li_ko ninki_tag_li">
							<div><a href="★"># テキスト</a></div>
						</li>
						<li class="taso_li taso_li_ko ninki_tag_li">
							<div><a href="★"># テキスト</a></div>
						</li>
					</ul>
			</li>
			<li class="menu-item nav_btn taso_li menuset_02 m_b_4030">
				<a href="★" class="top_menu-1 nav_btn_link">
					<p class="nav_btn_tit_L">全てのタグ</p>
				</a>
			</li> --}}


			@guest('web')
			<li class="menu-item nav_btn taso_li menuset_03 login_btn mobile_hide">
                <a href="{{ route('login') }}" class="top_menu-1 nav_btn_link">
                    <p class="nav_btn_tit_L">ログイン</p>
				</a>
			</li>
			<li class="menu-item nav_btn taso_li menuset_03 signup_btn mobile_hide">
                <a href="{{ route('user.pre_create') }}" class="top_menu-1 nav_btn_link">
                    <p>新規登録</p>
				</a>
			</li>
            @endguest


            @auth('web')
			<li class="menu-item nav_btn taso_li menuset_06">
				<a href="{{ route('user.purchased_projects') }}" class="top_menu-1 nav_btn_link">
					<p class="nav_btn_tit_L">応援購入したプロジェクト</p>
				</a>
			</li>
			<li class="menu-item nav_btn taso_li menuset_06">
				<a href="{{ route('user.payment_history') }}" class="top_menu-1 nav_btn_link">
					<p class="nav_btn_tit_L">購入履歴一覧</p>
				</a>
			</li>
			<li class="menu-item nav_btn taso_li menuset_06">
				<a href="{{ route('user.message.index') }}" class="top_menu-1 nav_btn_link">
					<p class="nav_btn_tit_L">ダイレクトメッセージ一覧</p>
				</a>
			</li>
			<li class="menu-item nav_btn taso_li menuset_06">
				<a href="{{ route('user.my_project.project.index') }}" class="top_menu-1 nav_btn_link">
					<p class="nav_btn_tit_L">マイプロジェクト管理</p>
				</a>
			</li>

			<li class="menu-item nav_btn taso_li menuset_06">
				<a href="{{ route('user.liked_projects') }}" class="top_menu-1 nav_btn_link">
					<p class="nav_btn_tit_L">お気に入り</p>
				</a>
			</li>
            @endauth

			{{-- <li class="menu-item nav_btn taso_li menuset_06">
				<a href="★" class="top_menu-1 nav_btn_link">
					<p class="nav_btn_tit_L">お問い合わせ</p>
				</a>
			</li>
			<li class="menu-item nav_btn taso_li menuset_06">
				<a href="★" class="top_menu-1 nav_btn_link">
					<p class="nav_btn_tit_L">よくあるご質問・ヘルプ</p>
				</a>
			</li> --}}
            @auth('web')
            <li class="menu-item nav_btn taso_li menuset_06">
				<a href="{{ route('user.profile') }}" class="top_menu-1 nav_btn_link">
					<p class="nav_btn_tit_L">プロフィール</p>
				</a>
			</li>
            <li class="menu-item nav_btn taso_li menuset_06">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="top_menu-1 top_menu_logout">ログアウト</button>
                </form>
			</li>
			<li class="menu-item nav_btn taso_li menuset_06">
				<a href="{{ route('user.withdraw') }}" class="top_menu-1 nav_btn_link">
					<p class="nav_btn_tit_L">退会について</p>
				</a>
			</li>
            @endauth


			{{-- <li class="menu-item nav_btn taso_li menuset_04 header_serch_box">
				<i class="fas fa-search"></i><input type="text" name="search_word" placeholder="キーワードを検索">
			</li> --}}

			<!--▼ ★★★ログイン時-->
            @auth('web')
            <li class="menu-item nav_btn taso_li menuset_03 login_btn mobile_hide">
                <a href="{{ route('user.profile') }}" class="top_menu-1 nav_btn_link">
                    <p class="nav_btn_tit_L">プロフィール</p>
				</a>
			</li>
			<li class="menu-item nav_btn taso_li menuset_03 signup_btn mobile_hide">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="top_menu-1 logout_btn">ログアウト</button>
                </form>
			</li>
            @endauth
			<!--▲ ★★★ログイン時-->
		</ul>
	</nav>

</div><!--/drower-menu-list-->
</div><!--/drower-menu-->

</div>
</div><!--/#menu-->

<style>
.pc-details-screen_header{ display: flex; flex-wrap: wrap; align-items: center; align-content: center; width: 100%; font-size: 1.3rem; color:#00AEBD ;}
.pc-details-screen_header div{ width: 120px; padding: 15px 0; text-align: center;}
.pdsh_item{ position: relative;}
.pdsh_current{ border-bottom: solid 2px #00AEBD;}
</style>

{{-- <div class="pc-details-screen_header">
	<div class="pdsh_item pdsh_current">プロジェクト<a href="#" class="cover_link"></a></div>
	<div class="pdsh_item">活動レポート<a href="#" class="cover_link"></a></div>
	<div class="pdsh_item">応援コメント<a href="#" class="cover_link"></a></div>
</div> --}}

</header>
</div>

<style>
/**他ページヘッダー**/
.pc-details-screen_header{ display: none;}
</style>
    @if (session('flash_message'))
        <div class="fan_alert fan_alert-success">
            {{ session('flash_message') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="fan_alert fan_alert-danger">
            <ul>
                @foreach($errors->all()  as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')

    <x-user.footer />

    <p id="page-top_btn">
        <a href="#wrapper"><i class="fas fa-chevron-circle-up"></i></a>
    </p>

</div><!--/wrapper-->
</body>

<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v11.0" nonce="KCBaCGBe"></script>
<script src="https://www.line-website.com/social-plugins/js/thirdparty/loader.min.js" async="async" defer="defer"></script>
@yield('script')
<script>
$(window).on('load', function(){
    var winW = $(window).width();
    var winH = $(window).height();
    var devW = 767;
    if (winW <= devW) {
    //767px以下の時の処理
    } else {
    //768pxより大きい時の処理
    }
    // toastr読み込み
    $(function() {
        //ドキュメントロード時に、toastr のオプションを設定する
        toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
        }
    });
    // toastr読み込み
});

/**nav**/
$(function() {
    var headNav = $("#header_wrap");
    var headNav_img_01 = $(".view_01");
    var headNav_img_02 = $(".view_02");

    //scrollだけだと読み込み時困るのでloadも追加
    $(window).on('load scroll', function () {
        //現在の位置が200px以上かつ、クラスfixedが付与されていない時
        if($(this).scrollTop() > 150 && headNav.hasClass('fixed') == false) {
            //headerの高さ分上に設定
            headNav.css({"top": '-100px'});
            //クラスfixedを付与
            headNav.addClass('fixed');

            headNav_img_02.removeClass('view_no');
            headNav_img_02.addClass('view_yes');

            headNav_img_01.removeClass('view_yes');
            headNav_img_01.addClass('view_no');

            $("#container").addClass('container_margin-top');

            //位置を0に設定し、アニメーションのスピードを指定
            headNav.animate({"top": 0},400);

        }
        //現在の位置が199px以下かつ、クラスfixedが付与されている時にfixedを外す
        else if($(this).scrollTop() < 149 && headNav.hasClass('fixed') == true){
            headNav.removeClass('fixed');

            headNav_img_02.removeClass('view_yes');
            headNav_img_02.addClass('view_no');

            headNav_img_01.removeClass('view_no');
            headNav_img_01.addClass('view_yes');

            $("#container").removeClass('container_margin-top');
        }
    });
});

//チェックボックス操作時
$(function(){
    $('input[name="nav-tgl"]').change(function() {
    if($(this).prop('checked')) {
        $('input[name="nav-tgl_clone"]').prop('checked',true);
    } else {
        $('input[name="nav-tgl_clone"]').prop('checked',false);
    }
    });

    $('#nav-tgl').on('change', function(){
    var st = $(window).scrollTop();
    if($(this).prop("checked") == true) {
        $('html').addClass('scroll-prevent');
        $('html').css('top', -(st) + 'px');
        $('#nav-tgl').on('change', function(){
        if($(this).prop("checked") !== true) {
            $('html').removeClass('scroll-prevent');
            $(window).scrollTop(st);
        }
        });
    }
    });
});

//【slick】
$(function(){
    var mainSlider = document.getElementById('slider'); //メインスライダーid
    var thumbnailSlider = document.getElementById('thumbnail_slider'); //サムネイルスライダーid
    $(mainSlider).slick({
    autoplay: true,
    speed: 2000,
    arrows: true,
    asNavFor: thumbnailSlider,
    dots: false,
    });
    $(thumbnailSlider).slick({
    slidesToShow: 5,
    speed: 2000,
    asNavFor: mainSlider,
    arrows: false,
    dots: false,
    });
    //#thumbnail_sliderでクリックしたスライドをカレントにする
    $(thumbnailSlider+" .slick-slide").on('click',function(){
    var index = $(this).attr("data-slick-index");
    $(thumbnailSlider).slick("slickGoTo",index,false);
    });
});

//ハンバーガー　メガナビ
jQuery(document).ready(function($) {
    $('#js-global-nav li').each(function(i) {

        $(this).hover(function() {
            var attr = this.getAttribute("id");
            //console.log(attr);
            var h_mega_nav = "#" + attr + "-js";
            //console.log(h_mega_nav);
            $(h_mega_nav).addClass("is-active");

        }, function() {
            var attr = this.getAttribute("id");
            //console.log(attr);
            var h_mega_nav = "#" + attr + "-js";
            $(h_mega_nav).removeClass("is-active");
        });
    });
    $("#page-top_btn").hide();
    $(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('#page-top_btn').fadeIn();
            } else {
                $('#page-top_btn').fadeOut();
            }
        });

        $('#page-top_btn a').click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    });
    //URLのハッシュ値を取得
    var urlHash = location.hash;
    //ハッシュ値があればページ内スクロール
    if(urlHash) {
        //スクロールを0に戻す
        $('body,html').stop().scrollTop(0);
        setTimeout(function () {
        //ロード時の処理を待ち、時間差でスクロール実行
        scrollToAnker(urlHash) ;
        }, 100);
    }

    //通常のクリック時
    $('a[href^="#"]').click(function() {
        //ページ内リンク先を取得
        var href= $(this).attr("href");
        //リンク先が#か空だったらhtmlに
        var hash = href == "#" || href == "" ? 'html' : href;
        //スクロール実行
        scrollToAnker(hash);
        //リンク無効化
        return false;
    });

    // 関数：スムーススクロール
    // 指定したアンカー(#ID)へアニメーションでスクロール
    function scrollToAnker(hash) {
        var target = $(hash);
        var position = target.offset().top;
        $('body,html').stop().animate({scrollTop:position}, 500);
    }
});



//【さらに条件を追加】ボタン
//全選択ボタンを取得する
const uncheckBtn = document.getElementById("ac_list_uncheck-btn");
//チェックボックスを取得する
const el = document.getElementsByClassName("ac_list_checks");

//全てのチェックボックスのチェックを外す
const uncheckAll = () => {
    for (let i = 0; i < el.length; i++) {
        el[i].checked = false;
    }
};
//全選択ボタンをクリックした時「uncheckAll」を実行
uncheckBtn.addEventListener("click", uncheckAll, false);
</script>
</html>
