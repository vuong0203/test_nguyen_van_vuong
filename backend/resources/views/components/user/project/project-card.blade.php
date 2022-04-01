@isset($project)
    <div class="{{ empty($cardSize) ? 'ib01R_01' : 'ib01L_01' }} {{$newProject()}}">
        <img src="{{ $projectImageUrl() }}">
        <a href="{{ route('user.project.show', ['project' => $project]) }}" class="cover_link"></a>
        <div class="{{ empty($cardSize) ? 'okini_link' : 'okini_link_L' }} liked_project" id="{{ $project->id }}">
        <i class="{{ $userLiked() ? 'far fa-heart' : 'fas fa-heart' }}"></i>
        </div>
    </div>

    @if(!empty($ranking))
    <div class="ib03L_rank">
        <div class="ib03L_rank_01"><x-user.crown :ranking="$ranking" size="large" /></div>
        <div class="ib03L_rank_02 E-font">{{ $ranking }}</div>
        <div class="ib03L_rank_03 E-font"></div>
    </div>
    @endif

    @if(!empty($cardSize))
    <div class="ib01L_cate_tag">
        @foreach($project->tags as $tag)
            <a href="{{ route('user.search', ['tag_id' => $tag->id, 'sort_type' => '0']) }}" class="cate_tag_link">{{ $tag->name }}</a>
        @endforeach
    </div>
    @endif

    <div class="{{ empty($cardSize) ? 'ib01R_02' : 'ib01L_02' }}">
        <div class="progress_arrow_box_wrapper">
            <div class="{{ ProgressBarState::getArrowBoxClassName($project) }}">{{ ProgressBarState::getArrowBoxText($project) }}</div>
        </div>
        <div class="progress-bar_par" style="width: {{ $project->achievement_rate }}%; max-width:100%">
            <div class="{{ ProgressBarState::getNumberClassName($project) }}">
                {{ $project->achievement_rate }}%
            </div>
        </div>
        <div class="progress-bar">
            <span
                style="width: {{ $project->achievement_rate }}%; max-width:100%"
                class="{{ ProgressBarState::getBarClassName($project) }}"
            ></span>
        </div>
    </div>

    <div class="{{ empty($cardSize) ? 'ib01R_03' : 'ib01L_03' }}">
        <h2>{{ Str::limit($project->title, 46) }}</h2>
        <a href="{{ route('user.project.show', ['project' => $project]) }}" class="cover_link"></a>
    </div>

    <div class="{{ empty($cardSize) ? 'ib01R_04' : 'ib01L_04' }}">
        <div>現在の支援総額 <span>{{ number_format($project->payments_sum_price) }}円</span></div>
        <div class="supporter_count">現在の支援者数 <span>{{ $project->payments_count }}人</span></div>
        @if (DateFormat::checkDateIsFuture($project->start_date))
            <div>残り <span>{{ DateFormat::getDiffCompareWithToday($project->start_date) }}</span></div>
        @elseif (DateFormat::checkDateIsPast($project->start_date) && DateFormat::checkDateIsFuture($project->end_date))
            <div>残り <span>{{ DateFormat::getDiffCompareWithToday($project->end_date) }}</span></div>
        @elseif (DateFormat::checkDateIsPast($project->end_date))
            <div>募集終了</div>
        @endif
    </div>
@endisset
