<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use App\CommentMessage;
use App\Page;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $title)
    {
        $page = Page::where('title', $title)->first();
        if (empty($page))
            return json_encode(array(
                'result' => 'false',
                'msg' => 'invalid title'
            ));
        if (!isset($request->order)) $request->order = "mostpopular";
        if ($request->order == "latest")
            $comments = Comment::where('page_id', $page->id)->orderBy('id', 'desc')->paginate(10);
        else if ($request->order == "mostpopular")
            $comments = Comment::where('page_id', $page->id)
                ->where('star_num', '>=', 10)
                ->orderBy('star_num', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(10);
        return view('comment', ['paginator' => $comments, 'order' => $request->order]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $title)
    {
        $page = Page::where('title', $title)->first();
        if (empty($page))
            return json_encode(array(
                'result' => 'false',
                'msg' => 'invalid title'
            ));

        $comment = new Comment();
        $comment->page_id = $page->id;
        $comment->user_id = $request->session()->get('user.id');
        $comment->content = $request->text;
        $comment->signature = "匿名用户";
        $comment->position = "打酱油评论";
        $comment->ban = false;
        $comment->star_num = 0;

        $comment->save();

        return json_encode(array(
            'result' => 'true',
            'msg' => 'success',
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}