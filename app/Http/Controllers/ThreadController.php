<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Http\Requests\StoreThreadRequest;
use App\Http\Requests\UpdateThreadRequest;
use App\Http\Responses\ErrorResponseJson;
use App\Http\Responses\ThreadResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThreadController extends Controller
{
    public int $perPage = 10;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $threads = DB::select("
            SELECT 
                threads.id,
                (SELECT COUNT(*) FROM posts
                WHERE posts.thread_id = threads.id) as post_count
            FROM threads
        ");

        if (!count($threads)) {
            $err = new ErrorResponseJson('No threads.');
            return $err->returnResponse(404);
        }

        $data = ['data' => $threads];

        return response(json_encode($data), 200)
        ->header('Content-Type', 'application/json');
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
    public function store(StoreThreadRequest $request)
    {
        $request->validate([
            'category_id' => 'required',
            'subject' => 'required',
            'content' => 'required'
        ]);

        $category = DB::select("
            SELECT * FROM categories
            WHERE id = ?
        ", [$request->category_id]);

        if (!count($category)) {
            $err = new ErrorResponseJson('Invalid Category ID');
            return $err->returnResponse(404);
        }

        DB::insert("
            INSERT INTO threads 
            (category_id, subject, content, user_id, posted_date)
            VALUES (?, ?, ?, ?, ?)
        ", [
            $request->category_id, $request->subject, $request->content,
            $request->user()->id, date('Y-m-d H:i:s')
        ]);

        $postThread = DB::select("
            SELECT * FROM threads WHERE id = LAST_INSERT_ID()
        ");
        
        return response([
            'message' => 'Success',
            'data' => $postThread[0]
        ], 201)
        ->header('Content-Type', 'application/json');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, string $page)
    {
        $thread = DB::select("
            SELECT
                threads.id as thread_id, 
                threads.subject as thread_subject, 
                threads.content as thread_content,
                threads.user_id as thread_author,
                threads.category_id,
                threads.posted_date as thread_posted_date,
                categories.name as category_name,
                users.name as thread_author_name,
                (SELECT COUNT(*) FROM posts 
                WHERE posts.thread_id = ?) as posts_count
            FROM threads
            JOIN users ON users.id = threads.user_id
            JOIN categories ON threads.category_id = categories.id
            WHERE threads.id = ?
        ", [$id, $id]);

        if (!count($thread)) {
            $err = new ErrorResponseJson("Thread doesn't exist");
            return $err->returnResponse(404);
        }

        $offset = ($this->perPage * (int) $page) - $this->perPage;
        
        $posts = DB::select("
            SELECT
                posts.id,
                posts.content,
                posts.user_id,
                posts.posted_date,
                users.name as user_name
            FROM posts
            JOIN users ON posts.user_id = users.id
            WHERE posts.thread_id = ?
            ORDER BY posts.posted_date ASC
            LIMIT ?
            OFFSET ?
        ", [$id, $this->perPage, $offset]);

        $response = ThreadResponse::show(
            $thread, 
            $posts, 
            ['thread_id', 'thread_subject', 'thread_content', 'thread_author',
            'category_name', 'category_id', 'thread_author_name', 'thread_posted_date',
            'posts_count'],
            ['id', 'content', 'user_id', 'posted_date', 'user_name'],
            'posts'
        );
        
        return response($response, 200)->header('Content-Type', 'application/json');       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateThreadRequest $request, int $id)
    {
        $request->validate([
            'content' => 'required',
            'subject' => 'required'
        ]);

        $thread = DB::select("
            SELECT * FROM threads
            WHERE id = ?
        ", [$id]);

        if (!count($thread)) {
            $err = new ErrorResponseJson('Invalid Thread ID');
            return $err->returnResponse(404);
        }

        if ($thread[0]->user_id !== $request->user()->id) {
            return response([
                'message' => 'Unauthorized'
            ], 403)
            ->header('Content-Type', 'application/json');
        }

        DB::update("
            UPDATE threads
            SET content = ?, subject = ?
            WHERE id = ?
        ", [$request->content, $request->subject, $id]);

        $newThread = DB::select("
            SELECT * FROM threads WHERE id = ?
        ", [$id]);

        return response([
            'data' => $newThread[0],
            'message' => 'Success'
        ], 200)->header('Content-Type', 'application/json');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $thread = DB::select("
            SELECT * FROM threads
            WHERE id = ?
        ", [$id]);

        if (!count($thread)) {
            $err = new ErrorResponseJson('Invalid thread ID');
            return $err->returnResponse(404);
        }

        $isAuthor = $thread[0]->user_id === $request->user()->id;

        // return var_dump($request->user()->is_admin);

        if (!$isAuthor && !$request->user()->is_admin) {
            return response([
                'message' => 'Unauthorized'
            ], 403)
            ->header('Content-Type', 'application/json');
        }

        DB::delete("
            DELETE FROM threads
            WHERE id = ?
        ", [$id]);

        DB::delete("
            DELETE FROM posts
            WHERE posts.thread_id = ?
        ", [$id]);

        return response([
            'message' => 'Success'
        ], 204);
    }
}
