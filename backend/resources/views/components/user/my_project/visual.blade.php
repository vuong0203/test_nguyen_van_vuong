<div class="form_item_row" style="margin-bottom: 40px;">
    <div class="form_item_tit" style="margin-bottom: 10px">スライド画像追加<span class="nini_txt">任意</span></div>
    <div class="input_file_button_wrapper">
        <label>
            <input type="file" hidden onChange="uploadProjectImage(this, {{ $project->id }})">
            <a class="input_file_button">
                ファイルを追加する
            </a>
        </label>
    </div>
</div>

<div class="form_item_row project_image_row">
    <div class="form_item_tit">スライド画像変更<span class="nini_txt">任意</span></div>

    @foreach($projectImages as $project_image)
        <div class="js-image__card">
            <div class="ib02_01 E-font my_project_img_wrapper my_project_img_wrapper_show">
                <img id="project_file_{{ $project_image->id }}" src="{{ Storage::url($project_image->file_url) }}">
                <button id="{{ $project_image->id }}" class="js-image_delete project_image-delete"><i class="fas fa-times-circle"></i></button>
            </div>
            <div class="form_item_tit" style="margin-bottom: 10px"></div>
            <div class="input_file_button_wrapper">
                <label>
                    <input hidden type="file" onChange="uploadProjectImage(this, {{ $project->id }}, {{ $project_image->id }})">
                    <a class="input_file_button">
                        ファイルを変更する
                    </a>
                </label>
            </div>
        </div>
    @endforeach
</div>

<form action="{{ route('user.my_project.project.update', ['project' => $project, 'current_tab' => 'visual']) }}" method="post">
    @csrf
    @method('PUT')
    <div class="form_item_row">
        <div class="form_item_tit">
            スライドYouTube動画URL設定
        </div>
        <input type="text" name="video_url" class="def_input_100p" value="{{ old('video_url', optional($projectVideo)->file_url) }}" oninput="updateMyProject.textInput(this, {{ $project->id }})">
        <x-common.async-submit-message propName="video_url" />
    </div>

    {{-- <div class="def_btn">
        <button type="submit" class="disable-btn">
            <p style="font-size: 1.8rem;font-weight: bold;color: #fff;">動画URLを保存する</p>
        </button>
    </div> --}}

    <x-common.navigating_page_buttons />
</form>
