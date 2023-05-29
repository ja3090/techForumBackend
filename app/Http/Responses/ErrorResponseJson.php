<?php

namespace App\Http\Responses;

class ErrorResponseJson {
    protected string $errorJson;

    public function __construct(string $message) {
        $response = [
            'error' => [
                'message' => $message,
            ],
            'data' => null
        ];

        $this->errorJson = json_encode($response);
    }

    public function returnResponse(int $responseCode) {
        return response($this->errorJson, $responseCode)
            ->header('Content-Type', 'application/json');
    }
}