<?php

namespace Tests\Unit;

use App\Http\Responses\CategoryResponse;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

class CategoryResponseTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_throws_exception_with_incorrect_input(): void
    {
        $resource = new stdClass();
        $resource->name = 'John Doe';
        $resource->email = 'jdoe@email.com';
        $resource->status = 'online';

        $resourceArray = [$resource];
        $errorMessage = "/Field 'foo' does not exist on this resource./";

        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches($errorMessage);

        CategoryResponse::index(
            $resourceArray,
            ['email', 'foo'],
            'bar'
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches($errorMessage);

        CategoryResponse::show(
            $resourceArray,
            ['email', 'foo'],
            ['status', 'name']
        );
    }
}
