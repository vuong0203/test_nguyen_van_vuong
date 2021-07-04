<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = Comment::with(['project', 'payment.user', 'reply'])
                            ->orderBy('created_at', 'DESC')
                            ->paginate(10);
                            // dd($comments);

        return view('admin.supporter_comment.index', compact('comments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function destroy(SupporterComment $supporter_comment)
    {
        $supporter_comment->delete();
        return redirect()->action([CommentController::class, 'index'])
                        ->with('flash_message', "支援者コメントの削除が完了しました");
    }

    public function search(SearchRequest $request)
    {
        $supporter_comments =
            Comment::searchByWords($request->getArrayWords())
                            ->searchByProject($request->project_id)
                            ->searchWithPostDates($request->from_date, $request->to_date)
                            ->with(['project', 'user', 'repliesToSupporterComment'])
                            ->orderBy('created_at', 'DESC')
                            ->paginate(10);

        return view('admin.supporter_comment.index', compact('supporter_comments'));
    }
}
