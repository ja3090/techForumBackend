<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Responses\ErrorResponseJson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request, int $threadId)
    {
        $request->validate([
            'content' => 'required'
        ]);

        $thread = DB::select("
            SELECT * FROM threads
            WHERE id = ?
        ", [$threadId]);

        if (!count($thread)) {
            $err = new ErrorResponseJson('Invalid Thread ID');
            return $err->returnResponse(404);
        }

        DB::insert("
            INSERT INTO posts 
            (thread_id, content, user_id, posted_date)
            VALUES (?, ?, ?, ?)
        ", [
            $threadId, $request->content,
            $request->user()->id, date(DATE_ATOM)
        ]);

        $post = DB::select("
            SELECT * FROM posts WHERE id = LAST_INSERT_ID()
        ");
        
        return response([
            'message' => 'Success',
            'data' => $post[0]
        ], 201)
        ->header('Content-Type', 'application/json');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $post)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, int $id)
    {
        $request->validate([
            'content' => 'required'
        ]);

        $post = DB::select("
            SELECT * FROM posts
            WHERE id = ?
        ", [$id]);

        if (!count($post)) {
            $err = new ErrorResponseJson('Invalid Post ID');
            return $err->returnResponse(404);
        }

        if ($post[0]->user_id !== $request->user()->id) {
            return response([
                'message' => 'Unauthorized'
            ], 403)
            ->header('Content-Type', 'application/json');
        }

        DB::update("
            UPDATE posts
            SET content = ?
            WHERE id = ?
        ", [$request->content, $id]);

        $newPost = DB::select("
            SELECT * FROM posts WHERE id = ?
        ", [$id]);

        return response([
            'data' => $newPost[0],
            'message' => 'Success'
        ], 200)->header('Content-Type', 'application/json');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $post = DB::select("
            SELECT * FROM posts
            WHERE id = ?
        ", [$id]);

        if (!count($post)) {
            $err = new ErrorResponseJson('Invalid Post ID');
            return $err->returnResponse(404);
        }
        
        $isAuthor = $post[0]->user_id === $request->user()->id;

        if (!$isAuthor && !$request->user()->is_admin) {
            return response([
                'message' => 'Unauthorized'
            ], 403)
            ->header('Content-Type', 'application/json');
        }

        DB::delete("
            DELETE from posts
            WHERE id = ?
        ", [$id]);

        return response([
            'message' => 'Success'
        ], 204);
    }
}
