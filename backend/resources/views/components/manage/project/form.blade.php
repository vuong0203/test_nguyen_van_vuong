<div class="form-group">
    <label>掲載ユーザー</label>
    <div class="dropdown">
        {{ Form::select('user_id', $users, old('user_id', optional($project ?? null)->user_id), ['class' => 'form-control']) }}
    </div>
</div>

<div class="form-group">
    <label>タイトル</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', optional($project ?? null)->title) }}">
</div>

<div class="form-group">
    <label>プロジェクト内容</label>
    <textarea type="text" name="content"
        class="form-control">{{old('content', optional($project ?? null)->content)}}</textarea>
</div>

<div class="form-group">
    <label>プロジェクトサポーターリターン内容</label>
    <textarea type="text" name="ps_plan_content"
       class="form-control">{{old('ps_plan_content', optional($project ?? null)->ps_plan_content)}}</textarea>
</div>

<div class="form-row">
    <div class="col-md-11 mb-3">
        <label>目標金額</label>
        <input type="number" name="target_amount" class="form-control mb-2 mr-sm-2" min="0"
            value="{{ old('target_amount', optional($project ?? null)->target_amount) }}" step="10000">
    </div>
    <h5 class="pt-5">円</h5>
</div>

<div class="form-group">
    <label>キュレーター</label>
    <input type="text" name="curator" class="form-control"
        value="{{ old('curator', optional($project ?? null)->curator) }}">
</div>

<div>
    <p>注意事項</p>
    <ul>
        <li>掲載開始日時は、現在〜1週間後以降に設定して下さい。(これは審査に1週間程度お時間を頂く為です。)</li>
        <li>掲載期間は60日までです。</li>
    </ul>
</div>
<div class="form-group">
    <label>掲載開始日時</label>
    <input type="text" name="start_date" class="form-control" id="start_date"
        value="{{ old('start_date', optional($project ?? null)->start_date) }}">
</div>

<div class="form-group">
    <label>掲載終了日時</label>
    <input type="text" name="end_date" class="form-control" id="end_date"
        value="{{ old('end_date', optional($project ?? null)->end_date) }}">
</div>

<div class="form-group">
    <div>タグ選択</div>
    <div class="d-flex flex-wrap">
        @foreach($tags as $key => $value)
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="{{ $key }}" name="tags[]" id="{{ 'flexCheckDefault_'.$key }}"
                {{ $projectTags !== null && in_array($key, $projectTags) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ 'flexCheckDefault_'.$key }}">
                {{ $value }}
            </label>
        </div>
        <div>&emsp;&emsp;&emsp;</div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label>トップ画像</label><br>
    <input id='imageUploader' type="file" name="images[]" multiple="multiple">
</div>

<div class="form-group">
    <label>動画URL</label>
    <div>
        <ul>
            <li>動画URLはYouTubeのURLを使用してください。</li>
        </ul>
    </div>
    <input type="text" name="video_url" class="form-control"
        value="{{ old('video_url', optional($projectVideo)->file_url) }}"
        placeholder="https://www.youtube.com/watch?v=xxxxxxxxxxxxxx">
    <section class="d-flex justify-content-left">
        <div class="col-sm-4">
            {{ DisplayVideoHelper::getVideoAtManage(optional($projectVideo)->file_url) }}
        </div>
    </section>
</div>

@if($project ?? false)
<button type="submit" class="btn btn-primary">更新</button>
@else
<button type="submit" class="btn btn-primary">作成</button>
@endif
