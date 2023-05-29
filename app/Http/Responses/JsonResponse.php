<?php

namespace App\Http\Responses;

class JsonResponse {

    public array $data;

    public function __construct()
    {
        $this->data = [];

        return $this;
    }
}