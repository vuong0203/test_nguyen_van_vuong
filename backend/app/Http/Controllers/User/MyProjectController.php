<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\MyProjectRequest;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\Plan;
use App\Models\Tag;
use Carbon\Carbon;
use Auth;
use Illuminate\Http\Request;

class MyProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = \Auth::user();
            return $next($request);
        });
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
        $tags = Tag::pluck('name', 'id');

        $project = $this->user->projects()->save(Project::initialize());

        $project->projectFiles()->save(ProjectFile::initialize());

        return view('user.my_project.edit', ['project' => $project, 'tags' => $tags]);
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
    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($id)
    // {
    //     //
    // }

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
        $project->fill($request->all())->save();

        $this->user->identification->fill($request->all())->save();

        $this->user->profile->fill($request->all())->save();

        $this->user->address->fill($request->all())->save();

        if($request->has('tags')){
            $project->tags()->detach();
            $project->tags()->attach(array_values($request->tags));
        }

        if ($request->has('image_url')){
            $file_array = [];
            foreach($request->image_url as $key => $value){
                if($request->file_ids !== null && in_array((string) $key, $request->file_ids, true)){
                    $project_file = ProjectFile::find($key);
                    $project_file->file_url = $value[0];
                    $project_file->save();
                } else {
                    $file_array[] =
                    ProjectFile::make([
                        'file_url' => $value[0],
                        'file_content_type' => 'image_url'
                    ]);
                };
                if ($file_array !== []){
                    $project->projectFiles()->saveMany($file_array);
                }
            }
        }

        if ($request->has('video_url') && $request->video_url !== null){
            $project->projectFiles()->save(
                ProjectFile::make([
                    'file_url' => $request->video_url,
                    'file_content_type' => 'video_url'
                ])
            );
        }
        return redirect()->action([MyProjectController::class, 'edit'], ['project' => $project])->with(['flash_message' => 'プロジェクトが更新されました。']);
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
}
