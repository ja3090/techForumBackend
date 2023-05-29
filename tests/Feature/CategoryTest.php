<?php

namespace Tests\Feature;

use Tests\Utils\NotFoundRes;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    // use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_empty_db_response_on_get(): void
    {
        $categoryEndpoints = [
            '/api/categories' => 'No categories were found.', 
            '/api/categories/1/1' => 'Can\'t find what you\'re looking for.'
        ];

        $tester = new NotFoundRes();
        
        $tester->returnsNotFound($this, $categoryEndpoints, 'get');
    }
}
