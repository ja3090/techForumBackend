<?php

namespace App\Http\Responses;

class ThreadResponse {

    public static function show(
        array $threadData,
        array $joinedTable,
        array $threadFields,
        array $joinedTableFields,
        string $joinedTableName
    ) {
        $jsonResponse = [];
        $jsonResponse["data"][$joinedTableName] = [];

        foreach ($threadFields as $threadField) {
            if (property_exists($threadData[0], $threadField)) {
                $jsonResponse["data"][$threadField] = $threadData[0]->$threadField;
            } else {
                throw new \Exception("Field '{$threadField}' does not exist on argument provided for threadData.");
            }
            
        }

        foreach ($joinedTable as $joinedResource) {
            $obj = [];

            foreach ($joinedTableFields as $joinedField) {
                if (property_exists($joinedResource, $joinedField)) {    
                    $obj[$joinedField] = $joinedResource->$joinedField;
                } else {
                    throw new \Exception("Field '{$joinedField}' does not exist on argument provided for joinedTable.");
                }
            }

            $jsonResponse["data"][$joinedTableName][] = $obj;
        }

        return json_encode($jsonResponse);
    }
        
}