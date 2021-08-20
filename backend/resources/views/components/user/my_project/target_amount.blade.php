<form action="{{ route('user.my_project.project.update', ['project' => $project, 'current_tab' => 'target_amount']) }}" method="post">
    @csrf
    @method('PUT')
    <div class="form_item_row">
        <div class="form_item_tit">
            目標金額<span class="hissu_txt">必須</span>
            <br/>
            <span class="disclaimer">
                ※目標金額は最低10,000円から設定可能です。
            </span>
        </div>
        <input type="number" name="target_amount" class="p-postal-code def_input_100p"
            value="{{ old('target_amount', optional($project)->target_amount) }}" placeholder="（例）100000" oninput="updateMyProject.textInput(this, {{ $project->id }})">
        <div class="spinner-wrapper">
            <div class="spinner" id="spinner_target_amount"></div>
            <p class="saved_icon" aria-hidden="true" style="display: none;" id="saved_target_amount">保存しました</p>
            <span id="errors_target_amount" style="color: red;"></span>
        </div>
    </div>

    <div class="form_item_row">
        <div class="form_item_tit">
            掲載開始日(日付、時刻)
            <span class="hissu_txt">必須</span>
            <br/>
            <span class="disclaimer">
                ※存在しない日付は選択できません。
            </span>
            <br/>
            <span class="disclaimer">
                ※審査期間があるため、2週間以降の日付を設定してください。
            </span>
        </div>
        <input type="text" id="start_date" name="start_date" class="p-postal-code def_input_100p"
            value="{{ old('start_date', optional($project)->start_date) }}" placeholder="（例）100000" oninput="updateMyProject.textInput(this, {{ $project->id }})">
        <div class="spinner-wrapper">
            <div class="spinner" id="spinner_start_date"></div>
            <p class="saved_icon" aria-hidden="true" style="display: none;" id="saved_start_date">保存しました</p>
            <span id="errors_start_date" style="color: red;"></span>
        </div>
    </div>

    <div class="form_item_row">
        <div class="form_item_tit">
            掲載終了日(日付、時刻)
            <span class="hissu_txt">必須</span>
            <br/>
            <span class="disclaimer">
                ※存在しない日付は選択できません。
            </span>
            <br/>
            <span class="disclaimer">
                ※掲載期間は最長60日で設定してください
            </span>
        </div>
        <input type="text" id="end_date" name="end_date" class="p-postal-code def_input_100p"
            value="{{ old('end_date', optional($project)->end_date) }}" placeholder="（例）100000" oninput="updateMyProject.textInput(this, {{ $project->id }})">
        <div class="spinner-wrapper">
            <div class="spinner" id="spinner_end_date"></div>
            <p class="saved_icon" aria-hidden="true" style="display: none;" id="saved_end_date">保存しました</p>
            <span id="errors_end_date" style="color: red;"></span>
        </div>
    </div>
    <x-common.save_back_button />
</form>
