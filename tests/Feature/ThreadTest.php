<?php

namespace Tests\Feature;

use Tests\Utils\NotFoundRes;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_empty_db_response_on_get(): void
    {
        $categoryEndpoints = [
            '/api/threads/1/1' => "Thread doesn't exist", 
            '/api/threads' => "No threads."
        ];

        $tester = new NotFoundRes();
        
        $tester->returnsNotFound($this, $categoryEndpoints, 'get');
    }

    public function test_empty_db_response_on_post(): void
    {
        $categoryEndpoints = [
            '/api/threads' => "Invalid Category ID"
        ];

        $tester = new NotFoundRes();
        
        $tester->returnsNotFound($this, $categoryEndpoints, 'post', [
            'category_id' => 1,
            'subject' => 'blah',
            'content' => 'blah',
        ]);
    }
}
