<?php

namespace Tests\Utils;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NotFoundRes
{
    /**
     * @param $categoryEndpoints
     * e.g. [
     *  '/api/foo' => 'Error: bar.'
     * ];
     */

    public function returnsNotFound(
        TestCase $testCase, 
        array $categoryEndpoints,
        string $httpMethod,
        array $data = []
    ): void
    {
        foreach ($categoryEndpoints as $endpoint => $message) {        
            $user = User::factory()->create();

            DB::shouldReceive('select')
                ->once()
                ->andReturn([]);

            $response = $testCase->withHeaders([
                'Accept' => 'application/json'
                ])
                ->actingAs($user)
                ->$httpMethod($endpoint, $data);

            $response
                ->assertStatus(404)
                ->assertJson([
                    'error' => [
                        'message' => $message
                    ],
                    'data' => null
                ]);
        }
    }
}
