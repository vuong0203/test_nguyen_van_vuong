<div class="prof_page_R">
@if(Request::get('input_type') === 'name')
    <form action="{{ route('user.update_profile', ['user' => Auth::user()]) }}" method="POST" name="nameForm">
        @method('PATCH')
        @csrf
        <div class="prof_edit_row">
            <div class="prof_edit_01">ユーザー名<br><span>編集中</span></div>
            <div class="prof_edit_editbox">
                <input name="name" type="text" placeholder="UserId" value="{{ old('name', Auth::user()->name) }}"/>
            </div>
            <div class="prof_edit_03">
                <a href="javascript:document.nameForm.submit()">更新</a>
            </div>
        </div>
    </form>
@elseif(Request::get('input_type') === 'image_url')
    <form action="{{ route('user.update_profile', ['user' => Auth::user()]) }}" method="POST" name="imageForm" enctype="multipart/form-data">
        @method('PATCH')
        @csrf
        <div class="prof_edit_row">
            <div class="prof_edit_01">プロフィール写真<br><span>編集中</span></div>
            <div class="prof_edit_editbox">
                <input type="file" id="files_input" name="image_url" accept=".png, .jpg, .jpeg, .gif"><br><span class="prof_edit_editbox_desc">ファイルサイズは1MB以下<br>ファイル形式は jpeg、gif、png 形式のみ可</span>
            </div>
            <div class="prof_edit_03">
                <a href="javascript:document.imageForm.submit()">更新</a>
            </div>
        </div>
    </form>
@elseif(Request::get('input_type') === 'email')
    <form action="{{ route('user.update_profile', ['user' => Auth::user()]) }}" method="POST" name="mailForm">
        @method('PATCH')
        @csrf
        <div class="prof_edit_row">
            <div class="prof_edit_01">メールアドレス<br><span>編集中</span></div>
            <div class="prof_edit_editbox">
                <div class="pee_tit pee_tit_first">現在のメールアドレス</div>
                <div class="pee_now">{{ Auth::user()->email }}</div>

                <div class="pee_tit">新しいメールアドレス<span class="prof_edit_editbox_desc">　半角英数字のみ</span></div>
                <input name="email" type="email"/>

                <div class="pee_tit">新しいメールアドレス（確認用）
                    {{-- <span class="prof_edit_editbox_desc">　コピー＆ペースト不可</span> --}}
                </div>
                <input name="email_confirmation" type="email"/>
            </div>
            <div class="prof_edit_03">
                <a href="javascript:document.mailForm.submit()">更新</a>
            </div>
        </div>
    </form>
@elseif(Request::get('input_type') === 'password')
    <form action="{{ route('user.update_profile', ['user' => Auth::user()]) }}" method="POST" name="passwordForm">
        @method('PATCH')
        @csrf
        <div class="prof_edit_row">
            <div class="prof_edit_01">パスワード<br><span>編集中</span></div>
            <div class="prof_edit_editbox">
                <div class="pee_tit pee_tit_first">現在のパスワード
                    <a href="{{ route('user.forgot_password') }}" class="pee_pass_link">
                        現在のパスワードを忘れた方はこちらから
                    </a>
                </div>
                <input name="current_password" type="password"/>

                <div class="pee_tit">パスワード<span class="prof_edit_editbox_desc">　半角英数字のみ</span></div>
                <input name="password" type="password"/>

                <div class="pee_tit">パスワード（確認用）
                    {{-- <span class="prof_edit_editbox_desc">　コピー＆ペースト不可</span> --}}
                </div>
                <input name="password_confirmation" type="password"/>

            </div>
            <div class="prof_edit_03">
                <a href="javascript:document.passwordForm.submit()">更新</a>
            </div>
        </div>
    </form>
@elseif(Request::get('input_type') === 'sns_links')
    <form action="{{ route('user.update_profile', ['user' => Auth::user()]) }}" method="POST" name="urlForm">
        @method('PATCH')
        @csrf
        <div class="prof_edit_row">
            <div class="prof_edit_01">URL<br><span>編集中</span></div>
            <div class="prof_edit_editbox">
                <input name="twitter_url" type="text" placeholder="UserId">
                <input name="instagram_url" type="text" placeholder="UserId">
                <input name="youtube_url" type="text" placeholder="UserId">
                <input name="tiktok_url" type="text" placeholder="UserId">
                <input name="other_url" type="text" placeholder="UserId">
            </div>
            <div class="prof_edit_03">
                <a href="javascript:document.urlForm.submit()">更新</a>
            </div>
        </div>
    </form>
@elseif(Request::get('input_type') === 'prefecture_id')
    <form action="{{ route('user.update_profile', ['user' => Auth::user()]) }}" method="POST" name="prefectureForm">
        @method('PATCH')
        @csrf
        <div class="prof_edit_row">
            <div class="prof_edit_01">現在地<br><span>編集中</span></div>
            <div class="prof_edit_editbox pee_select_hori">
                <div class="cp_ipselect cp_normal">
                    <select name="prefecture">
                        @foreach( PrefectureHelper::getPrefectures() as $key => $value)
                        <option value="{{ $value }}" {{optional(Auth::user()->address)->prefecture == $value || old('prefecture') === $value ?  'selected' : ''}}>{{$value}}</option>
                        @endforeach
                    </select>
                </div><!-- /.cp_ipselect -->

            </div><!-- prof_edit_editbox -->
            <div class="prof_edit_03">
                <a href="javascript:document.prefectureForm.submit()">更新</a>
            </div>
        </div>
    </form>


@elseif(Request::get('input_type') === 'birthday')

<form action="{{ route('user.update_profile', ['user' => Auth::user()]) }}" method="POST" name="birthdayForm">
    @method('PATCH')
    @csrf
    <div class="prof_edit_row">
        <div class="prof_edit_01">生年月日<br><span>編集中</span></div>
        <div class="prof_edit_editbox pee_select_hori">
            <div class="cp_ipselect cp_normal">
                <select id="year" name="year">
                    <option value="">年</option>
                    <?php $years = array_reverse(range(today()->year - 100, today()->year)); ?>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ old('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <div class="cp_ipselect cp_normal">
                <select id="month" name="month">
                    <option value="">月</option>
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" {{ old('month') == $month ? 'selected' : '' }}>{{ $month }}</option>
                    @endforeach
                </select>
            </div>

            <div class="cp_ipselect cp_normal">
                <select id="day" name="day" data-old-value="{{ old('day') }}"></select>
            </div>
            <div class="cp_ipselect cp_normal">
                <select name="birthday_is_published">
                    <option value="1">公開する</option>
                    <option value="0" >公開しない</option>
                </select>
            </div>
        </div>
        <div class="prof_edit_03">
            <a href="javascript:document.birthdayForm.submit()">更新</a>
        </div>
    </div>
</form>

@elseif(Request::get('input_type') === 'gender')
    <form action="{{ route('user.update_profile', ['user' => Auth::user()]) }}" method="POST" name="genderForm">
        @method('PATCH')
        @csrf
        <div class="prof_edit_row">
			<div class="prof_edit_01">性別<br><span>編集中</span></div>
			<div class="prof_edit_editbox pee_select_hori">
				<div class="cp_ipselect cp_normal">
					<select name="gender">
                        <option value="女性">女性</option>
						<option value="男性">男性</option>
                        <option value="その他">その他</option>
					</select>
				</div>
				<div class="cp_ipselect cp_normal">
					<select name="gender_is_published">
                        <option value="1">公開する</option>
                        <option value="0">公開しない</option>
                    </select>
				</div>
			</div>
			<div class="prof_edit_03">
                <a href="javascript:document.genderForm.submit()">更新</a>
            </div>
		</div>
    </form>
@elseif(Request::get('input_type') === 'introduction')
    <form action="{{ route('user.update_profile', ['user' => Auth::user()]) }}" method="POST" name="introductionForm">
        @method('PATCH')
        @csrf
        <div class="prof_edit_row">
			<div class="prof_edit_01">自己紹介<br><span>編集中</span></div>
			<div class="prof_edit_editbox">
				<textarea rows="8" name="introduction"></textarea>
			</div>
			<div class="prof_edit_03">
                <a href="javascript:document.introductionForm.submit()">更新</a>
            </div>
        </div>
    </form>
@else
    <div class="prof_edit_row">
        <div class="prof_edit_01">ユーザー名</div>
        <div class="prof_edit_02">{{ Auth::user()->name }}</div>
        <div class="prof_edit_03">
            編集
            <a href="{{ route('user.profile', ['input_type' => 'name']) }}" class="cover_link"></a>
        </div>
    </div>
    <div class="prof_edit_row">
        <div class="prof_edit_01">プロフィール写真</div>
        <div class="prof_edit_02"></div>
        <div class="prof_edit_03">
            編集
            <a href="{{ route('user.profile', ['input_type' => 'image_url']) }}" class="cover_link"></a>
        </div>
    </div>
    <div class="prof_edit_row">
        <div class="prof_edit_01">メールアドレス</div>
        <div class="prof_edit_02">{{ Auth::user()->email }}</div>
        <div class="prof_edit_03">
            編集
            <a href="{{ route('user.profile', ['input_type' => 'email']) }}" class="cover_link"></a>
        </div>
    </div>
    <div class="prof_edit_row">
        <div class="prof_edit_01">パスワード</div>
        <div class="prof_edit_02">********</div>
        <div class="prof_edit_03">
            編集
            <a href="{{ route('user.profile', ['input_type' => 'password']) }}" class="cover_link"></a>
        </div>
    </div>
    <div class="prof_edit_row">
        <div class="prof_edit_01">URL</div>
        {{-- <div class="prof_edit_02">{{ Auth::user()->sns_links->twitter_url }}</div>
        <div class="prof_edit_02">{{ Auth::user()->sns_links->instagram_url }}</div>
        <div class="prof_edit_02">{{ Auth::user()->sns_links->youtube_url }}</div>
        <div class="prof_edit_02">{{ Auth::user()->sns_links->tiktok_url }}</div>
        <div class="prof_edit_02">{{ Auth::user()->sns_links->other_url }}</div> --}}
        <div class="prof_edit_03">
            編集
            <a href="{{ route('user.profile', ['input_type' => 'sns_links']) }}" class="cover_link"></a></div>
    </div>
    <div class="prof_edit_row">
        <div class="prof_edit_01">現在地</div>
        <div class="prof_edit_02">{{ optional(Auth::user()->address)->prefecture }}</div>
        <div class="prof_edit_03">
            編集
            <a href="{{ route('user.profile', ['input_type' => 'prefecture_id']) }}" class="cover_link"></a></div>
    </div>
    <div class="prof_edit_row">
        <div class="prof_edit_01">生年月日</div>
        <div class="prof_edit_02">
            @if(isset(Auth::user()->profile->birthday))
                {{ Auth::user()->profile->birthday }}
            @else
                設定されていません
            @endif
        </div>
        <div class="prof_edit_03">編集<a href="{{ route('user.profile', ['input_type' => 'birthday']) }}" class="cover_link"></a></div>
    </div>
    <div class="prof_edit_row">
        <div class="prof_edit_01">性別</div>
        <div class="prof_edit_02">
            @if(isset(Auth::user()->profile->gender))
                {{ Auth::user()->profile->gender }}
            @else
                設定されていません
            @endif
        </div>
        <div class="prof_edit_03">編集<a href="{{ route('user.profile', ['input_type' => 'gender']) }}" class="cover_link"></a></div>
    </div>
    <div class="prof_edit_row">
        <div class="prof_edit_01">自己紹介</div>
        <div class="prof_edit_02">
        @if(isset(Auth::user()->profile->introduction))
            {{ Auth::user()->profile->introduction }}
        @else
            設定されていません
        @endif
        </div>
        <div class="prof_edit_03">編集<a href="{{ route('user.profile', ['input_type' => 'introduction']) }}" class="cover_link"></a></div>
    </div>
@endif
</div>
