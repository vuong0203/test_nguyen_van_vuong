<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Project\ProjectService;
use App\Http\Requests\MyProjectRequest;
use App\Http\Requests\ProjectFileRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\Plan;
use App\Models\Tag;
use App\Services\View\EditMyProjectTabService;
use Carbon\Carbon;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Storage;
use Log;

class MyProjectController extends Controller
{

    protected $project_service;

    protected $user;

    protected $my_project_tab_service;

    public function __construct(ProjectService $project_service, EditMyProjectTabService $my_project_tab_service)
    {
        $this->middleware(function ($request, $next) {
            $this->user = \Auth::user();
            return $next($request);
        });

        $this->project_service = $project_service;

        $this->my_project_tab_service = $my_project_tab_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = $this->user->projects()->get();
        return view('user.my_project.index', ['projects' => $projects->load('projectFiles')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $project = $this->user->projects()->save(Project::initialize());

        return redirect()->action([MyProjectController::class, 'edit'], ['project' => $project]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    // NOTICE こちらのshowでキャンプファイアのダッシュボード的な扱いになるかと思います。
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        $project->getWithPaymentsCountAndSumPrice();
        return view('user.my_project.show', ['project' => $project]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        $tags = Tag::pluck('name', 'id');

        return view('user.my_project.edit', ['project' => $project, 'tags' => $tags]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MyProjectRequest $request, Project $project)
    {
        DB::beginTransaction();
        try {
            $project->fill($request->all())->save();

            $this->user->identification->fill($request->all())->save();

            $this->user->profile->fill($request->all())->save();

            $this->user->address->fill($request->all())->save();

            $this->project_service->attachTags($project, $request);

            $this->project_service->saveVideoUrl($project, $request);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return redirect()->action([MyProjectController::class, 'edit'], ['project' => $project, 'next_tab' => $this->my_project_tab_service->getNextTab($request->current_tab)])->with(['flash_message' => 'プロジェクトが更新されました。']);
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

    public function uploadEditorFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);
        $path = $request->file('file')->store('public/image');
        return ['location' => Storage::url($path)];
    }

    public function uploadProjectImage(Project $project, ProjectFile $project_file = null, ProjectFileRequest $request)
    {
        $this->project_service->saveImage($project, $project_file, $request);

        session()->flash('flash_message', 'スライド画像の更新が完了しました。');
        return response()->json([
            'status' => 200,
            'redirect_url' => route('user.my_project.project.edit', ['project' => $project, 'next_tab' => 'visual']),
        ], 200);
    }

    public function apply(Project $project)
    {
        try {
            $project->release_status = '承認待ち';
            $project->update();
            return redirect()->action([MyProjectController::class, 'index'], ['projects' => $this->user->projects()->get()->load('projectFiles')])->with(['flash_message' => 'プロジェクトの申請が完了しました。']);
        } catch (Exception $e) {
            Log::alert($e->getMessage(), $e->getTrace());
            throw $e;
        }
    }
}
