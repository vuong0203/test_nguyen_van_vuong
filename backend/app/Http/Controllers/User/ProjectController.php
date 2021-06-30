<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Plan\CalculationForPrice;
use App\Traits\UniqueToken;
use App\Models\Tag;
use App\Models\User;
use App\Models\Project;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Comment;
use App\Models\Profile;
use App\Models\Address;
use App\Actions\PayJp\PayJpInterface;
use App\Http\Requests\ConfirmPaymentRequest;
use App\Models\ProjectTagTagging;
use App\Models\UserProjectLiked;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Str;

class ProjectController extends Controller
{

    public function __construct(PayJpInterface $pay_jp_interface)
    {
        $this->middleware(function ($request, $next) {
            $this->user = \Auth::user();
            return $next($request);
        });

        $this->pay_jp = $pay_jp_interface;
    }

    public function index()
    {
        // 応援プロジェクト（目標金額の高い順）
        $cheer_projects = Project::getReleasedProject()->seeking()->orderBy('target_amount', 'DESC')
            ->inRandomOrder()->takeWithRelations(9)->get();

        // 最新のプロジェクト
        $new_projects = Project::getReleasedProject()->seeking()->orderBy('created_at', 'DESC')
            ->takeWithRelations(9)->get();

        // 人気のプロジェクト
        $popularity_projects = Project::getReleasedProject()->seeking()->ordeyByLikedUsers()
            ->takeWithRelations(9)->get();

        // 募集終了が近いプロジェクト
        $nearly_deadline_projects = Project::getReleasedProject()->seeking()->orderByNearlyDeadline()
            ->inRandomOrder()->takeWithRelations(9)->get();

        // もうすぐ公開のプロジェクト
        $nearly_open_projects = Project::getReleasedProject()->orderByNearlyOpen()
            ->inRandomOrder()->takeWithRelations(9)->get();

        return view('user.index', compact(
            'new_projects',
            'cheer_projects',
            'popularity_projects',
            'nearly_deadline_projects',
            'nearly_open_projects'
        ));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Project $project)
    {
        return view('user.project.show', ['project' => $project->load([
                'projectFiles',
                'plans',
                'plans.includedPayments',
                'plans.includedPayments.user',
                'reports' => function ($query) {
                    $query->orderByDesc('created_at');
                },
            ])]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Select a plans
     *
     * @param Project
     * @return \Illuminate\Http\Response
     */
    public function selectPlans(Project $project)
    {
        $project->load('plans');
        return view('user.project.select_plan', ['project' => $project]);
    }

    /**
     * Confirm selected plans
     *
     * @param Project
     * @return \Illuminate\Http\Response
     */
    public function confirmPayment(Project $project, ConfirmPaymentRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->user->profile->fill($request->all())->save();
            $this->user->address->fill($request->all())->save();
            $plans = Plan::whereIn('id', array_keys($request->plans))->get();
            $price_amount = CalculationForPrice::getPriceAmount($request->plans, $plans);
            $unique_token = UniqueToken::getToken();
            $payment = Payment::make([
                'price' => $price_amount,
                'message_status' => 'ステータスなし',
                'merchant_payment_id' => $unique_token,
                'pay_jp_id' => $request->payjp_token,
                'payment_is_finished' => false,
                'remarks' => $request->remarks
            ]);
            $comment = Comment::make([
                'project_id' => $project->id,
                'content' => $request->comments,
            ]);
            $this->user->payments()->save($payment)
                ->each(function($payment) use ($plans, $comment){
                    $payment->includedPlans()->saveMany($plans);
                    $payment->comment()->save($comment);
                });
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return view('user.project.confirm_plan', ['project' => $project, 'payment' => $payment]);
    }

    /**
     * do payment for Pay Jp
     *
     *@param App\Models\Project
     *@param App\Models\Payment
     *
     *@return \Illuminate\Http\Response
     */
    public function paymentForPayJp(Project $project, Payment $payment)
    {
        $response = $this->pay_jp->Payment($payment->price, $payment->pay_jp_id);
        DB::beginTransaction();
        try {
                $payment->payment_is_finished = true;
                $payment->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                $this->pay_jp->Refund($response->id);
                throw $e;
            }

        $supporter_count = User::getCountOfSupportersWithProject($project);
        $total_amount = Payment::getTotalAmountOfSupporterWithProject($project);

        return view('user.plan.supported', ['project' => $project, 'payment' => $payment, 'supporter_count' => $supporter_count, 'total_amount' => $total_amount]);
    }

    /**
     * Display a project list page searched from any parameter.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $projectsQuery = Project::query();
        if($request->tag_id){
            $tags = Tag::pluck("name", "id");
        } else {
            $tags = null;
        }

        //カテゴリ絞り込み
        if ($request->tag_id) {
            $projectsQuery->whereIn('id',
                ProjectTagTagging::query()->select('project_id')
                ->whereIn('tag_id',
                    Tag::query()->select('id')
                    ->find($request->tag_id)
                )
            );
        }
        // こちらはデザインにはなかったのでコメントアウト致しました。
        // //フリーワード絞り込み
        // if ($request->free_word) {
        //     // 全角スペースを半角スペースに変換
        //     $words = str_replace("　", " ", $request->free_word);
        //     // 半角スペースごとに区切って配列に代入
        //     $array_words = explode(" ", $words);
        //     //この部分今のところタイトルと説明文でしか検索できてないです...アイドル名がなぜかうまくいかなかったのでまたやります...
        //     foreach ($array_words as $array_word) {
        //         $projectsQuery->where(function ($projectsQuery) use ($array_word) {
        //             $projectsQuery->Where('projects.title', 'like', "%$array_word%")
        //                 ->orWhere('explanation', 'like', "%$array_word%")
        //                 ->orWhereIn('talent_id', Talent::select('id')
        //                 ->where('name', 'like', "%$array_word%"));
        //         });
        //     }
        // }
        // sort_typeによって順序変更
        // 0 => 人気順(募集中のお気に入り数順),   1 => 新着順,   2 => 終了日が近い順,   3 => 支援総額順,   4 => 支援者数順
        switch ($request->sort_type) {
            case '0':
                $projectsQuery->seeking()->ordeyByLikedUsers();
                break;

            case '1':
                $projectsQuery->seeking()->orderBy('created_at', 'DESC');
                break;

            case '2':
                if(strstr($request->fullUrl(), '/search?sort_type=2')){
                    // ヘッダーメニューの「募集終了が近いプロジェクトの場合」(現在掲載中且つ、残り1週間で終了)
                    $projectsQuery->daysLeftSeeking('end_date')->orderByNearlyDeadline();
                } else {
                    // 検索画面の「並び替え」にある「終了日が近い順」(現在掲載中のもの)
                    $projectsQuery->seeking()->orderByNearlyDeadline();
                }
                break;

            case '3':
                $projectsQuery->ordeyByFundingAmount();
                break;

            case '4':
                $projectsQuery->ordeyByNumberOfSupporters();
                break;
        }

        //募集中かどうか確認
        switch ($request->holding_check) {
            // 公開前
            case '0':
                if (strstr($request->fullUrl(), '/search?holding_check=0')) {
                    // ユーザーヘッダーメニューの「もうすぐ公開されます」(1週間以内に公開)
                    $projectsQuery->beforeSeeking()->daysLeftSeeking('start_date');
                } else {
                    // 検索画面の「募集状況」にある「公開日前」
                    $projectsQuery->beforeSeeking();
                }
                break;
            // 支援募集中
            case '1':
                $projectsQuery->seeking();
                break;
            // 募集終了
            case '2':
                $projectsQuery->afterSeeking();
                break;
        }

        // こちらもお気に入りプロジェクト検索はデザインにはないのでコメントアウト
        // // ユーザーのログイン機能追加必須？
        // if ($request->cheered_check) {
        //     $projectsQuery->OnlyCheeringDisplay();
        // }

        $projects = $projectsQuery->where('release_status', '掲載中')->with('tags')->paginate(9);

        return view('user.search', compact('projects', 'tags'));
    }

    // こちらもデザインにないので一旦コメントアウトしておきます。
    // public function ProjectLiked(Request $request)
    // {
    //     $userLiked = UserProjectLiked::where('user_id', Auth::id())->where('project_id', $request->project_id)->first();

    //     if (Auth::id() === null) {
    //         return $result = "未ログイン";
    //     } elseif ($userLiked !== null) {
    //         $userLiked->delete();
    //         return $result = "削除";
    //     } else {
    //         $project_liked = new UserProjectLiked(['user_id' => Auth::id()]);
    //         $project_liked->project_id = $request->project_id;
    //         $project_liked->save();
    //         return $result = "登録";
    //     }
    // }

    public function support(Project $project)
    {
        $this->authorize('checkIsFinishedPayment', $project);
        if (!isset(Auth::user()->profile->inviter_code)) {
            $value = [
                'inviter_code' => Str::uuid()
            ];
            Auth::user()->saveProfile($value);
            Auth::user()->load('profile');
        }
        $encrypted_code = Crypt::encrypt(Auth::user()->profile->inviter_code);
        $invitation_url = route('user.project.show', ['project' => $project, 'inviter' => $encrypted_code]);
        Auth::user()->supportedProjects()->attach($project->id);

        return view('user.project.support', ['invitation_url' => $invitation_url]);
    }
}
