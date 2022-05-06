<?php

function arrayToObject(array $array) {
    $object = new StdClass();

    if (is_array($array) && count($array) > 0) {
        foreach ($array as $key => $value) {
            $key = strtolower(trim($key));
            if (!empty($key)) {
                $object->$key = arrayToObject($value);
            }
        }
        return $object;
    }
    else {
        return false;
    }
}

function objectToArray($object) {
    if (is_array($object) || is_object($object)) {
        $result = [];
        foreach ($object as $key => $value) {
            $result[$key] = (is_array($value) || is_object($value)) ? objectToArray($value) : $value;
        }
        return $result;
    }
    return $object;
}