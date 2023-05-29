<?php

namespace Tests\Unit;

use App\Http\Responses\ThreadResponse;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

class ThreadResponseTest extends TestCase
{
    /**
     * A basic unit test example.
     */

    public function test_throws_exception_with_incorrect_input_for_thread_data(): void
    {
        $firstResource = new stdClass();
        $firstResource->name = 'John Doe';
        $firstResource->email = 'jdoe@email.com';

        $firstResourceArray = [$firstResource];

        $secondResource = new stdClass();
        $secondResource->hobby = 'blah';

        $secondResourceArray = [$secondResource];

        $errorMessage = "/Field 'foo' does not exist on argument provided for threadData./";

        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches($errorMessage);

        ThreadResponse::show(
            $firstResourceArray,
            $secondResourceArray,
            ['foo'],
            ['hobby'],
            'hobby'
        );

        $secondErrorMessage = "/Field 'bar' does not exist on argument provided for joinedTable./";

        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches($secondErrorMessage);

        ThreadResponse::show(
            $firstResourceArray,
            $secondResourceArray,
            ['email'],
            ['barf'],
            'hobby'
        );
    }

    public function test_throws_exception_with_incorrect_input_for_joined_table(): void
    {
        $firstResource = new stdClass();
        $firstResource->name = 'John Doe';
        $firstResource->email = 'jdoe@email.com';

        $firstResourceArray = [$firstResource];

        $secondResource = new stdClass();
        $secondResource->hobby = 'blah';

        $secondResourceArray = [$secondResource];

        $secondErrorMessage = "/Field 'bar' does not exist on argument provided for joinedTable./";

        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches($secondErrorMessage);

        ThreadResponse::show(
            $firstResourceArray,
            $secondResourceArray,
            ['email'],
            ['bar'],
            'hobby'
        );
    }
}
