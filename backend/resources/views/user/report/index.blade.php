@extends('user.layouts.base')

@section('title', '活動報告一覧')

@section('content')
<section id="supported-projects" class="section_base">

    <div class="tit_L_01 E-font">
        <h2>REPORTS</h2>
        <div class="sub_tit_L">活動報告一覧</div>
    </div>

    <div class="prof_page_base inner_item reports">
        <div class="prof_page_L">
            <x-user.mypage-navigation-bar/>
        </div>

        <div class="prof_page_R">
            <table>
                <tr>
                    <th>作成日</th>
                    <!-- TODO:こちらは将来必要になった際に追加する -->
                    <!-- <th>公開設定</th> -->
                    <th>タイトル</th>
                    <th>編集</th>
                </tr>
                @if($reports)
                    @foreach($reports as $report)
                    <tr class="prof_edit_row">
                        <td>
                            <div>{{ $report->created_at->format('Y年m月d日') }}<br>
                            <span>{{ $report->created_at->format('H:i') }}</span></div>
                        </td>
                        <td>
                          {{ $report->title }}
                        </td>
                        <td>
                            <div>
                                <p class="mobile_display">{{ $report->created_at->format('Y年m月d日 H:i') }}</p>
                                <a href="{{ route('user.report.edit' ,['project' => $project, 'report' => $report]) }}">
                                  <i class="far fa-edit fa-lg"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </table>
            
            <div class="def_btn">
              <button type="submit" class="disable-btn">
                <a href="{{ route('user.report.create' ,['project' => $project ]) }}" style="font-size: 1.8rem;font-weight: bold;color: #fff;">新規投稿を作成</a>
              </button>
            </div>

            <div style="margin: 100px 0;">
              <x-common.pagination :props="$reports"/>
            </div>
        </div>
    </div>
</section>
@endsection

<style>
  /* テーブル */
  .reports table {
    width: 63%;
  }

  .reports tr:first-child {
    width: 110%;
    display: flex;
    background: #F7FDFF;
    color: #00AEBD;
  }

  .reports tr:first-child th {
    margin: 20px;
  }

  .reports th:nth-child(2) {
    width: 535px;
  }

  .reports .prof_edit_row td:nth-child(2) {
    width: 400px;
  }
  
  .prof_edit_row {
    width: 110%;
  }

  /* アイコン */
  .reports .mobile_display { 
    display: none; 
  }

  .reports a { 
    color: #00AEBD; 
  }

  /* 新規投稿ボタン */
  .reports .def_btn { margin: 70px 170px 100px; }

  /* スマホサイズ */
  @media (max-width: 767px) {
    /* テーブルの各スタイル */
    .reports tr:first-child { width: calc(100% - 20%); display: none;)}
    /* .reports th:nth-child(3) { width: 100%;} */
    .reports .prof_edit_row { width: calc(200% - 40%); }
    .reports .prof_edit_row td:first-child { display: none; }
    .reports .prof_edit_row td:nth-child(2) { width:100%; margin-top: 25px; margin-bottom: 15px;}
    .reports .prof_edit_row td:nth-child(3) { width:100%;}
    .reports .prof_edit_row td:nth-child(3) div { color: #00AEBD; font-size: 85%; }
    /* アイコン */
    .reports .fa-edit {
    position: relative;
    bottom: 17px;
    left: calc(100% - 15%);
    }
    .reports .mobile_display { display: block; }
    /* 新規投稿ボタン */
    .reports .def_btn { margin: 50px 0px 100px; }
  }

  /* PCサイズ */
  @media (min-width: 767px) {
    .reports .prof_edit_row td {
      height: 100px;
      display: flex;
      align-items: center;
    }
    .reports .fa-edit, .reports .prof_edit_row td:nth-child(2) {
      position: relative;
      right: 40px;
    }
  }
</style>