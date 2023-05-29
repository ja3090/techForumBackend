<?php

namespace App\Http\Responses;

use stdClass;

class CategoryResponse {

    public static function index(
        array $dbResponse,
        array $joinedFields,
        string $joinedTableName
    ) {
        $jsonResponse = new stdClass();

        foreach ($dbResponse as $resource) {
            $obj = [];

            foreach ($joinedFields as $field) {
                if (property_exists($resource, $field)) {
                    $obj[$joinedTableName][$field] = $resource->$field;
                    unset($resource->$field);
                } else {
                    throw new \Exception(
                        "Field '{$field}' does not exist on this resource."
                    );
                }
            }

            foreach ($resource as $key => $value) {
                $obj[$key] = $value;
            }

            $jsonResponse->data[] = $obj;
        }

        return json_encode($jsonResponse);
    }

    public static function show(
        array $dbResponse,
        array $joinedFields,
        array $topLevelFields
    ) {
        $jsonResponse = new stdClass();

        $jsonResponse->data = new stdClass();
        $jsonResponse->data->threads = [];

        foreach ($topLevelFields as $field) {
            $jsonResponse->data->$field = $dbResponse[0]->$field;
        }

        foreach ($dbResponse as $resource) {
            $obj = [];

            foreach ($joinedFields as $field) {
                if (property_exists($resource, $field)) {
                    $obj[$field] = $resource->$field;
                } else {
                    throw new \Exception(
                        "Field '{$field}' does not exist on this resource."
                    );
                }
            }

            $jsonResponse->data->threads[] = $obj;
        }

        return json_encode($jsonResponse);
    }
}