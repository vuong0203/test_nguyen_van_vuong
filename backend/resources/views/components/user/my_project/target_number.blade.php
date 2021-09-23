<form action="{{ route('user.my_project.project.update', ['project' => $project, 'current_tab' => 'target_number']) }}" method="post">
    @csrf
    @method('PUT')
    <div class="form_item_row">
        <div class="form_item_tit">
            目標人数<span class="hissu_txt">必須</span>
            <br/>
            <span class="disclaimer">
                ※目標人数は最低1人以上設定してください。
            </span>
        </div>
        <input type="number" name="target_number" class="p-postal-code def_input_100p"
            value="{{ old('target_number', optional($project)->target_number) }}" placeholder="（例）1000" oninput="updateMyProject.textInput(this, {{ $project->id }})">
        <x-common.async-submit-message propName="target_number" />
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
        <x-common.async-submit-message propName="start_date" />
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
        <x-common.async-submit-message propName="end_date" />
    </div>
    <x-common.navigating_page_buttons />
</form>
