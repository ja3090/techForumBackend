<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Responses\CategoryResponse;
use App\Http\Responses\ErrorResponseJson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public int $perPage = 10;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = DB::select("
            SELECT
                categories.id, 
                categories.name,
                threads.subject as thread_subject, 
                threads.posted_date as recent_thread_posted_date,
                threads.id as thread_id,
                (SELECT COUNT(*)
                FROM threads
                WHERE threads.category_id = categories.id) as thread_count,
                (SELECT users.name
                FROM users
                WHERE threads.user_id = users.id) as user_name
            FROM categories
            JOIN threads 
            ON threads.id = (
            SELECT id
                FROM threads
                WHERE threads.category_id = categories.id
                ORDER BY posted_date DESC
                LIMIT 1
            )
        ");


        if (!count($categories)) {
            $errorResponse = new ErrorResponseJson('No categories were found.');
            return $errorResponse->returnResponse(404);
        }
        
        $response = CategoryResponse::index(
            $categories,
            ['thread_subject', 'recent_thread_posted_date', 'thread_id'],
            'latest_thread' 
        );

        return response($response, 200)
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
    public function store(StoreCategoryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id, $page) {

        if (!$page) {
            $errorResponse = new ErrorResponseJson("Please request a page number.");
            return $errorResponse->returnResponse(400);
        }

        $offset = ($this->perPage * (int) $page) - $this->perPage;

        $category = DB::select("
            SELECT 
                users.name as author,
                threads.subject as thread_subject,
                threads.content as thread_content,
                threads.user_id as user_id,
                users.name as user_name,
                categories.name as category_name ,
                threads.category_id as category_id,
                threads.id as thread_id,
                threads.posted_date as thread_posted_date,
                (SELECT COUNT(*)
                FROM threads
                WHERE threads.category_id = categories.id) as thread_count,
                (SELECT COUNT(*)
                FROM posts
                WHERE threads.id = posts.thread_id) as post_count
            FROM categories
            JOIN threads
            ON threads.category_id = categories.id
            JOIN users
            ON threads.user_id = users.id
            WHERE threads.category_id = ?
            ORDER BY threads.posted_date DESC
            LIMIT ?
            OFFSET ?
        ", [$id, $this->perPage, $offset]);

        if (!count($category)) {
            $errorResponse = new ErrorResponseJson("Can't find what you're looking for.");
            return $errorResponse->returnResponse(404);
        }
        
        $response = CategoryResponse::show(
            $category,
            ['thread_subject', 'thread_id', 'thread_posted_date',
            'thread_content', 'author', 'user_id', 'post_count', 'user_name'],
            ['category_id', 'category_name', 'thread_count']
        );

        return response($response, 200)
        ->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
